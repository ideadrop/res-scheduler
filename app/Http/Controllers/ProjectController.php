<?php namespace App\Http\Controllers;

use App\Allocation;
use App\Description;
use App\Http\Requests;
use App\Jobs\DeleteProjectAllocationMail;
use App\Jobs\EditedProjectAllocationMail;
use App\Jobs\ProjectAllocationMail;
use App\Project;
use App\Projectroleuser;
use App\Role;
use App\Skill;
use App\Skillsused;
use App\Tag;
use App\Tagsused;
use App\User;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Mail;

class ProjectController extends Controller
{

    /**
     * Dashboard.
     *
     * @since  03/06/2017
     * @author Renny M Roy <renny.roy@cubettech.com>
     * @return view
     */
    public function dashboard()
    {
        return view('pages.dashboard');
    }

    /**
     * Gets all project lists.
     *
     * @since  03/06/2017
     * @author Renny M Roy <renny.roy@cubettech.com>
     * @return view
     */
    public function listProject(Request $request)
    {
        $perPage = 20;
        $projects = Project::orderBy('id', 'DESC')->paginate($perPage);
        $skills = Skill::all();
        return view('projects.index', compact('projects', 'skills'))->with('i',
            ($request->input('page', 1) - 1) * $perPage);
    }

    public function getTags(Request $request)
    {
        $query = $request->get('query');
        $tags = Tag::select(
            DB::raw('name as label'),
            DB::raw('name as value')
        )->whereRaw('name LIKE "%' . $query . '%"')->get();
        return \Response::json(['items' => $tags], 200);
    }

    public function getSkills(Request $request)
    {
        $query = $request->get('query');
        $skills = Skill::select(
            DB::raw('name as label'),
            DB::raw('name as value')
        )->whereRaw('name LIKE "%' . $query . '%"')->get();
        return \Response::json(['items' => $skills], 200);
    }

    public function getManagers(Request $request)
    {
        $query = $request->get('query');
        $managerRoleId = DB::table('roles')->where('name', '=', 'project_manager')->pluck('id');
        $managerIds = DB::table('role_user')->where('role_id', '=', $managerRoleId)->lists('user_id');
        $managers = User::leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
            ->selectRaw('users.id as value, CONCAT(profiles.first_name, " ", profiles.last_name) AS label')
            ->enabled()
            ->whereRaw('CONCAT(profiles.first_name, " ", profiles.last_name) LIKE "%' . $query . '%"')
            ->whereIn('users.id', $managerIds)
            ->get();

        if (count($managers) == 0) {
            $managers = [
                [
                    'value' => '',
                    'label' => 'No results found'
                ]
            ];
        }
        return \Response::json(['managers' => $managers], 200);
    }

    public function getDevelopers(Request $request)
    {
        $query = $request->get('query');
        $selected = $request->get('selected');
        $selectedIds = commaToArray($selected);

        $manIds = DB::table('roles')->where('name', '=', 'project_manager')->orWhere('name', '=', 'admin')->lists('id');
        $devIds = DB::table('role_user')->whereNotIn('role_id', $manIds)->lists('user_id');
        $devs = User::leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
            ->selectRaw('users.id as value, CONCAT(profiles.first_name, " ", profiles.last_name) AS label')
            ->enabled()
            ->allocatable()
            ->whereRaw('CONCAT(profiles.first_name, " ", profiles.last_name) LIKE "%' . $query . '%"')
            ->whereIn('users.id', $devIds)
            ->whereNotIn('users.id', $selectedIds)
            ->get();

        if (count($devs) == 0) {
            $devs = [
                [
                    'value' => '',
                    'label' => 'No results found'
                ]
            ];
        }

        return \Response::json(['devs' => $devs], 200);
    }

    public function createProject(Request $request)
    {
        $reqs = $request->all();
        $reqs['resources'] = trim($reqs['resources']);

        DB::beginTransaction();
        try {


            /*manager allocation checking starts*/
            /*$pmId = (int) $reqs['project-manager'];
            $pm = User::find($pmId);

            $assignablePercentage = getAllocatablePercentage(
                [
                'start_date' => $reqs['start-date'],
                'end_date' => $reqs['end-date'],
                'resource_id' => $pmId
                ]
            );
            if($assignablePercentage<25) {
                return response()->json(
                    [
                    'status' => 'error',
                    'message' => ucfirst($pm->profile->first_name).' could not be assigned as PM for this project, user has other allocations.'
                    ]
                );
            }*/
            /*manager allocation checking ends*/


            /*conflict checking starts*/
            if (trim($reqs['resource_confirm']) == '') :
                $inputs = [
                    'start_date' => $reqs['start-date'],
                    'end_date' => $reqs['end-date']
                ];

                $resourcesIds = commaToArray($reqs['resources']);

                $allocationConflicts = DB::table('allocations as al')
                    ->select(
                        'p.name',
                        'al.allocation_value',
                        'al.assignee_id as user_id',
                        DB::raw('DATE(al.start_date) as start_date'),
                        DB::raw('CONCAT(prf.first_name, " ", prf.last_name) AS username'),
                        DB::raw('DATE(al.end_date) as end_date')
                    )
                    ->join('projects as p', 'al.project_id', '=', 'p.id')
                    ->join('profiles as prf', 'al.assignee_id', '=', 'prf.user_id')
                    ->whereIn('al.assignee_id', $resourcesIds)
                    ->whereRaw(" ((DATE(al.start_date) >= '" . $inputs['start_date'] . "' AND DATE(al.start_date) < '" . $inputs['end_date'] . "') OR (DATE(al.end_date) > '" . $inputs['start_date'] . "' AND DATE(al.end_date) <= '" . $inputs['end_date'] . "') OR ('" . $inputs['start_date'] . "' > DATE(al.start_date) AND '" . $inputs['start_date'] . "' < DATE(al.end_date)) OR ('" . $inputs['end_date'] . "' > DATE(al.start_date) AND '" . $inputs['end_date'] . "' < DATE(al.end_date)))")
                    ->orderBy('al.assignee_id', 'DESC')
                    ->get();
                if (count($allocationConflicts) > 0) :
                    $confirmErrorHTML = '<strong>Resource allocations found in selected date range:</strong>';
                    $usedUserIds = [];
                    foreach ($allocationConflicts as $key => $allocation) {

                        if (!in_array($allocation->user_id, $usedUserIds)) {
                            array_push($usedUserIds, $allocation->user_id);
                            $confirmErrorHTML .= "<br><strong>" . $allocation->username . "</strong>";
                        }

                        $confirmErrorHTML .= sprintf(
                            '<br/>%s: %s allocation found on project %s from %s till %s',
                            ($key + 1),
                            $allocation->allocation_value . "%",
                            '"' . $allocation->name . '"',
                            $allocation->start_date,
                            $allocation->end_date
                        );

                    }
                    return response()->json(
                        [
                            'status' => 'confirm',
                            'message' => $confirmErrorHTML
                        ]
                    );
                endif;
            endif;

            /*conflict checking ends*/

            //Insert basic project Data
            $project = Project::create(
                [
                    'name' => $reqs['project-title'],
                    'start_date' => $reqs['start-date'] . ' 00:00:00',
                    'end_date' => $reqs['end-date'] . ' 00:00:00',
                    'project_code' => $reqs['project-code'],
                ]
            );

            //Manage Tags

            $tagArray = commaToArray($reqs['project-tags']);

            foreach ($tagArray as $tKey => $tag) {
                $tag = trim($tag);
                if ($tag == '') {
                    continue;
                }

                $tagExists = Tag::where('name', '=', $tag)->first();

                if (!is_null($tagExists)) {
                    Tagsused::create(
                        [
                            'tag_type' => 'project',
                            'tag_id' => $tagExists->id,
                            'target_id' => $project->id
                        ]
                    );
                } else {
                    //Insert into tags table
                    $tagObj = Tag::create(['name' => $tag, 'author_id' => Auth::user()->id]);
                    //Insert into tags_used table
                    Tagsused::create(['tag_type' => 'project', 'tag_id' => $tagObj->id, 'target_id' => $project->id]);
                }
            }

            /*manange skills starts*/
            $skillsArray = commaToArray($reqs['project-skills']);
            foreach ($skillsArray as $sKey => $skill) {
                $skill = trim($skill);
                if ($skill == '') {
                    continue;
                }

                if (str_replace(" ", "", $skill) != "") {
                    $skillExists = Skill::where('name', '=', $skill)->first();

                    if (!is_null($skillExists)) {
                        Skillsused::create(
                            [
                                'item_type' => 'project',
                                'skill_id' => $skillExists->id,
                                'item_id' => $project->id
                            ]
                        );
                    } else {
                        //Insert into skills table
                        $skillObj = Skill::create(['name' => $skill, 'author_id' => Auth::user()->id]);
                        //Insert into skills_used table
                        Skillsused::create(
                            [
                                'item_type' => 'project',
                                'skill_id' => $skillObj->id,
                                'item_id' => $project->id
                            ]
                        );
                    }
                }
            }
            /*manage skills ends*/

            /* Add PM default allocations starts*/
            /*Allocation::create(
                [
                'assignee_id' => $pmId,
                'assigner_id' => $pmId,
                'project_id' => $project->id,
                'start_date' => $reqs['start-date'],
                'end_date' => $reqs['end-date'],
                'allocation_type' => 'percentage',
                'allocation_value' => 25,
                ]
            );*/
            /* Add PM default allocations ends*/

            //Add Project Manager
            Projectroleuser::create(
                [
                    'user_id' => $reqs['project-manager'],
                    'project_id' => $project->id
                ]
            );


            //Add resources
            $resArray = explode(',', $reqs['resources']);
            foreach ($resArray as $rKey => $res) {
                Projectroleuser::create(
                    [
                        'user_id' => trim($res),
                        'project_id' => $project->id
                    ]
                );
            }

            //Manage Notes
            if (isset($reqs['project-note']) && $reqs['project-note'] != '') {
                Description::create(
                    [
                        'item_id' => $project->id,
                        'item_type' => 'project',
                        'value' => $reqs['project-note']
                    ]
                );
            }

            $msg = 'Project created successfully';
            $request->session()->flash('success', $msg);

            DB::commit();

            return response()->json(
                [
                    'status' => 'success',
                    'message' => $msg,
                    'redirect_url' => route('project.list')
                ]
            );

        } catch (\Exception $e) {

            DB::rollback();

            return response()->json(
                [
                    'status' => 'error',
                    'message' => $msg
                ]
            );


        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table("projects")->where('id', $id)->delete();
        //Remove Tags used

        Tagsused::where(['tag_type' => 'project', 'target_id' => $id])->delete();

        //Remove roles for this project

        Projectroleuser::where(['project_id' => $id])->delete();

        //Remove Notes for this project

        Description::where(['item_id' => $id, 'item_type' => 'project'])->delete();

        //Remove used Skills
        Skillsused::where('item_type', 'project')->where('item_id', '=', $id)->delete();

        Allocation::where('project_id', $id)->delete();

        return redirect()->route('project.list')
            ->with('success', 'Project deleted successfully');
    }

    public function getEdit($id)
    {
        try {
            $project = Project::where('id', '=', $id)->first();
            if (!is_null($project)) {
                $project->start_date = date('Y-m-d', strtotime($project->start_date));
                $project->end_date = date('Y-m-d', strtotime($project->end_date));
                $ownerRoleId = Role::where('name', '=', 'owner')->value('id');
                $managerRoleId = Role::where('name', '=', 'project_manager')->value('id');
                $leadRoleId = Role::where('name', '=', 'team_lead')->value('id');
                $devRoleId = Role::where('name', '=', 'developer')->value('id');

                $pm = Projectroleuser::select(
                    DB::raw('CONCAT(profiles.first_name, " ", profiles.last_name) AS label'),
                    DB::raw('profiles.user_id AS value')
                )
                    ->leftJoin('role_user', 'role_user.user_id', '=', 'projects_used.user_id')
                    ->leftJoin('profiles', 'profiles.user_id', '=', 'projects_used.user_id')
                    ->where('projects_used.project_id', '=', $id)
                    ->where('role_user.role_id', '=', $managerRoleId)
                    ->first();
                $devs = Projectroleuser::select(
                    DB::raw('CONCAT(profiles.first_name, " ", profiles.last_name) AS label'),
                    DB::raw('profiles.user_id AS value')
                )
                    ->leftJoin('role_user', 'role_user.user_id', '=', 'projects_used.user_id')
                    ->leftJoin('profiles', 'profiles.user_id', '=', 'projects_used.user_id')
                    ->where('projects_used.project_id', '=', $id)
                    ->where('role_user.role_id', '!=', $managerRoleId)
                    ->get();

                $tags = Tagsused::select(DB::raw('tags.name as label'), DB::raw('tags.name as value'))
                    ->leftJoin('tags', 'tags.id', '=', 'tags_used.tag_id')
                    ->where('tag_type', '=', 'project')
                    ->where('target_id', '=', $id)
                    ->get();
                $skills = Skillsused::select(DB::raw('skills.name as label'), DB::raw('skills.name as value'))
                    ->leftJoin('skills', 'skills.id', '=', 'skills_used.skill_id')
                    ->where('item_type', '=', 'project')
                    ->where('item_id', '=', $id)
                    ->get();
                $note = Description::select('value')
                    ->where('item_type', '=', 'project')
                    ->where('item_id', '=', $id)
                    ->first();
                return \Response::json(
                    [
                        'status' => 'success',
                        'project' => $project->toArray(),
                        'project_manager' => $pm,
                        'developers' => $devs,
                        'tags' => $tags->toArray(),
                        'skills' => $skills->toArray(),
                        'note' => $note
                    ], 200
                );
            } else {
                return \Response::json(
                    [
                        'status' => 'failure',
                        'message' => 'Project not found'
                    ], 404
                );
            }
        } catch (Exception $e) {
            return \Response::json(
                [
                    'status' => 'failure',
                    'message' => $e->getMessage() . ' on ' . $e->getCode() . ' line:' . $e->getLine()
                ], 422
            );
        }
    }

    public function postUpdate(Request $request)
    {
        $reqs = $request->all();

        DB::beginTransaction();
        try {

            /*conflict checking starts*/
            if (trim($reqs['resource_confirm']) == '') :
                $inputs = [
                    'start_date' => $reqs['edit-start-date'],
                    'end_date' => $reqs['edit-end-date']
                ];

                $resourcesIds = commaToArray($reqs['edit-resources']);

                $allocationConflicts = DB::table('allocations as al')
                    ->select(
                        'p.name',
                        'al.allocation_value',
                        'al.assignee_id as user_id',
                        DB::raw('DATE(al.start_date) as start_date'),
                        DB::raw('CONCAT(prf.first_name, " ", prf.last_name) AS username'),
                        DB::raw('DATE(al.end_date) as end_date')
                    )
                    ->join('projects as p', 'al.project_id', '=', 'p.id')
                    ->join('profiles as prf', 'al.assignee_id', '=', 'prf.user_id')
                    ->where('al.project_id', '!=', $reqs['project_id'])
                    ->whereIn('al.assignee_id', $resourcesIds)
                    ->whereRaw(" ((DATE(al.start_date) >= '" . $inputs['start_date'] . "' AND DATE(al.start_date) < '" . $inputs['end_date'] . "') OR (DATE(al.end_date) > '" . $inputs['start_date'] . "' AND DATE(al.end_date) <= '" . $inputs['end_date'] . "') OR ('" . $inputs['start_date'] . "' > DATE(al.start_date) AND '" . $inputs['start_date'] . "' < DATE(al.end_date)) OR ('" . $inputs['end_date'] . "' > DATE(al.start_date) AND '" . $inputs['end_date'] . "' < DATE(al.end_date)))")
                    ->orderBy('al.assignee_id', 'DESC')
                    ->get();
                if (count($allocationConflicts) > 0) :
                    $confirmErrorHTML = '<strong>Resource allocations found in selected date range:</strong>';
                    $usedUserIds = [];
                    foreach ($allocationConflicts as $key => $allocation) {

                        if (!in_array($allocation->user_id, $usedUserIds)) {
                            array_push($usedUserIds, $allocation->user_id);
                            $confirmErrorHTML .= "<br><strong>" . $allocation->username . "</strong>";
                        }

                        $confirmErrorHTML .= sprintf(
                            '<br/>%s: %s allocation found on project %s from %s till %s',
                            ($key + 1),
                            $allocation->allocation_value . "%",
                            '"' . $allocation->name . '"',
                            $allocation->start_date,
                            $allocation->end_date
                        );

                    }
                    return response()->json(
                        [
                            'status' => 'confirm',
                            'message' => $confirmErrorHTML
                        ]
                    );
                endif;
            endif;

            /*conflict checking ends*/

            //Insert basic project Data
            $project = Project::where('id', '=', $reqs['project_id'])->update(
                [
                    'name' => $reqs['edit-project-title'],
                    'start_date' => $reqs['edit-start-date'] . ' 00:00:00',
                    'end_date' => $reqs['edit-end-date'] . ' 00:00:00',
                    'project_code' => $reqs['edit-project-code'],
                ]
            );

            //Manage Tags

            $tagArray = commaToArray($reqs['edit-project-tags']);

            $usedTags = [];

            foreach ($tagArray as $tKey => $tag) {
                $tag = trim($tag);
                if ($tag == '') {
                    continue;
                }

                $tagExists = Tag::where('name', '=', $tag)->first();

                if (!is_null($tagExists)) {
                    //Check whether it already added
                    $addedTag = Tagsused::where('tag_id', $tagExists->id)
                        ->where('tag_type', 'project')
                        ->where('target_id', '=', $reqs['project_id'])
                        ->first();
                    //If no add new tag to project
                    if (is_null($addedTag)) {
                        Tagsused::create(
                            [
                                'tag_type' => 'project',
                                'tag_id' => $tagExists->id,
                                'target_id' => $reqs['project_id']
                            ]
                        );
                    }
                    $usedTags[] = $tagExists->id;
                } else {
                    //Insert into tags table
                    $tagObj = Tag::create(['name' => $tag, 'author_id' => Auth::user()->id]);
                    //Insert into tags_used table
                    Tagsused::create(
                        [
                            'tag_type' => 'project',
                            'tag_id' => $tagObj->id,
                            'target_id' => $reqs['project_id']
                        ]
                    );
                    $usedTags[] = $tagObj->id;
                }
            }

            //Delete removed Tags
            Tagsused::whereNotIn('tag_id', $usedTags)->where('tag_type', 'project')->where(
                'target_id', '=',
                $reqs['project_id']
            )->delete();

            //Manage skills

            $skillArray = commaToArray($reqs['edit-project-skills']);

            $usedSkills = [];

            foreach ($skillArray as $tKey => $skill) {
                $skill = trim($skill);
                if ($skill == '') {
                    continue;
                }

                $skillExists = Skill::where('name', '=', $skill)->first();

                if (!is_null($skillExists)) {
                    //Check whether it already added
                    $addedSkill = Skillsused::where('skill_id', $skillExists->id)
                        ->where('item_type', 'project')
                        ->where('item_id', '=', $reqs['project_id'])
                        ->first();
                    //If no add new skill to project
                    if (is_null($addedSkill)) {
                        Skillsused::create(
                            [
                                'item_type' => 'project',
                                'skill_id' => $skillExists->id,
                                'item_id' => $reqs['project_id']
                            ]
                        );
                    }
                    $usedSkills[] = $skillExists->id;
                } else {
                    //Insert into skills table
                    $skillObj = Skill::create(['name' => $skill, 'author_id' => 2]);
                    //Insert into skills_used table
                    Skillsused::create(
                        [
                            'item_type' => 'project',
                            'skill_id' => $skillObj->id,
                            'item_id' => $reqs['project_id']
                        ]
                    );
                    $usedSkills[] = $skillObj->id;
                }
            }

            //Delete removed Skills
            Skillsused::whereNotIn('skill_id', $usedSkills)->where('item_type', 'project')->where(
                'item_id', '=',
                $reqs['project_id']
            )->delete();

            //Manage Roles
            //
            //            //Manage Project Manager
            $projectId = $reqs['project_id'];
            $managerRoleId = Role::where('name', '=', 'project_manager')->value('id');
            $managerIds = DB::table('role_user')->where('role_id', '=', $managerRoleId)->lists('user_id');
            $oldManagerId = Projectroleuser::where('project_id', '=', $projectId)
                ->whereIn('user_id', $managerIds)
                ->value('user_id');
            $newManagerId = $reqs['edit-project-manager'];

            //if any array value is returned PM is deleted and added new PM
            $this->addOrRemoveManagerFromProject($newManagerId, $oldManagerId, $projectId);

            //Add resources
            $resourceIds = commaToArray($reqs['edit-resources']);

            //Delete resources who not in new list
            $cantRemoveUsers = $this->removeResourceFromProject($resourceIds, $oldManagerId, $newManagerId, $projectId);

            foreach ($resourceIds as $rKey => $res) {
                $res = trim($res);
                if ($res == '' || in_array($res, $cantRemoveUsers)) {
                    continue;
                }

                $resExists = Projectroleuser::where(
                    [
                        'user_id' => trim($res),
                        'project_id' => $reqs['project_id']
                    ]
                )
                    ->first();
                if (is_null($resExists)) {
                    Projectroleuser::create(
                        [
                            'user_id' => trim($res),
                            'project_id' => $reqs['project_id']
                        ]
                    );
                }
            }
            //
            //            //Manage Notes
            if (isset($reqs['edit-project-note']) && trim($reqs['edit-project-note']) != '') {
                $descrExists = Description::where(
                    [
                        'item_id' => $reqs['project_id'],
                        'item_type' => 'project'
                    ]
                )->first();
                if (!is_null($descrExists)) {
                    Description::where(
                        [
                            'item_id' => $reqs['project_id'],
                            'item_type' => 'project'
                        ]
                    )->update(['value' => $reqs['edit-project-note']]);
                } else {
                    Description::create(
                        [
                            'item_id' => $reqs['project_id'],
                            'item_type' => 'project'
                        ]
                    );
                }
            } elseif (isset($reqs['edit-project-note']) && trim($reqs['edit-project-note']) == '') {
                Description::where(
                    [
                        'item_id' => $reqs['project_id'],
                        'item_type' => 'project'
                    ]
                )->delete();
            } elseif (!isset($reqs['edit-project-note'])) {
                Description::where(
                    [
                        'item_id' => $reqs['project_id'],
                        'item_type' => 'project'
                    ]
                )->delete();
            }

            DB::commit();

            if (count($cantRemoveUsers) > 0) {

                $cantRemoveUserProfile = DB::table('profiles')
                    ->select(
                        DB::raw('CONCAT(profiles.first_name, " ", profiles.last_name) AS full_name')
                    )
                    ->whereIn('user_id', $cantRemoveUsers)
                    ->get();
                $removeMessage = '';
                foreach ($cantRemoveUserProfile as $key => $profile) {
                    $removeMessage .= (($key > 0) ? ', ' : '') . $profile->full_name;
                }

                $msg = 'Project updated successfully, But ' . $removeMessage . ' could not be removed from project due to their future allocation in this project. Kindly delete their future allocations and try again';
                $request->session()->flash('info', $msg);

                DB::commit();

                return response()->json(
                    [
                        'status' => 'info',
                        'message' => $msg,
                        'redirect_url' => route('project.list')
                    ]
                );
            }

            $msg = 'Project updated successfully';
            $request->session()->flash('info', $msg);

            return response()->json(
                [
                    'status' => 'success',
                    'message' => $msg,
                    'redirect_url' => route('project.list')
                ]
            );
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(
                [
                    'status' => 'error',
                    'message' => $e->getMessage() . ' on ' . $e->getCode() . ' line:' . $e->getLine()
                ]
            );
        }
    }

    public function addOrRemoveManagerFromProject($newManagerId, $oldManagerId, $projectId)
    {

        if ($newManagerId != $oldManagerId):

            $today = Carbon::now()->toDateString();

            $existingManagerFutureAllocation = DB::table('allocations as al')
                ->where('al.assignee_id', '=', $oldManagerId)
                ->where('al.project_id', '=', $projectId)
                ->whereRaw("DATE(al.end_date) >= '$today'")
                ->count();
            if ($existingManagerFutureAllocation == 0) {
                Projectroleuser::create(
                    [
                        'user_id' => $newManagerId,
                        'project_id' => $projectId,
                    ]
                );
                DB::table('projects_used')
                    ->where('project_id', '=', $projectId)
                    ->where('user_id', '=', $oldManagerId)
                    ->delete();
                //return [$oldManagerId];
            }else{
                //return [];
            }
        else:
            //return [];
        endif;

    }

    public function removeResourceFromProject($resourceArray, $oldManagerId, $newManagerId, $projectId)
    {
        $deleteResourceIds = Projectroleuser::whereNotIn('user_id', $resourceArray)
            ->where('project_id', '=', $projectId)
            ->lists('user_id');

        $today = Carbon::now()->toDateString();

        $futureAllocationUserIds = DB::table('users')
            ->whereExists(function ($query) use ($today, $projectId) {
                $query->select(DB::raw(1))
                    ->from('allocations as al')
                    ->whereRaw('al.assignee_id = users.id')
                    ->where('al.project_id', '=', $projectId)
                    ->whereRaw("DATE(al.end_date) >= '$today'");
            })
            ->whereIn('id', $deleteResourceIds)
            ->lists('id');

        DB::table('projects_used')
            ->where('project_id', '=', $projectId)
            ->whereNotIn('user_id', [$oldManagerId, $newManagerId])
            ->whereIn('user_id', $deleteResourceIds)
            ->whereNotIn('user_id', $futureAllocationUserIds)
            ->delete();
        
        return $futureAllocationUserIds;
    }

    public function getShow($id)
    {
        try {
            $project = Project::where('id', '=', $id)->first();
            if (!is_null($project)) {


                $resources = DB::table('profiles')
                    ->select(
                        'user_id as id', DB::raw('CONCAT(first_name, " ", last_name) AS title')
                    )
                    ->join('users', 'profiles.user_id', '=', 'users.id')
                    ->where('users.disabled', '=', 0)
                    ->where('users.allocatable', '=', 1)
                    ->whereExists(
                        function ($query) use ($project) {
                            $query->select(DB::raw(1))
                                ->from('projects_used')
                                ->whereRaw('profiles.user_id = projects_used.user_id')
                                ->where('projects_used.project_id', '=', $project->id);
                        }
                    )
                    ->get();

                return view(
                    'projects.show', [
                        'project' => $project,
                        'resources' => $resources
                    ]
                );
            } else {

                return redirect()->route('project.list')
                    ->withError('Project not found');
            }
        } catch (Exception $e) {
            return redirect()->route('project.list')
                ->withError($e->getCode() . ' ' . $e->getMessage());
        }
    }

    public function getView($id)
    {
        try {
            $project = Project::where('id', '=', $id)->first();
            if (!is_null($project)) {
                $project->start_date = date('Y-m-d', strtotime($project->start_date));
                $project->end_date = date('Y-m-d', strtotime($project->end_date));
                $ownerRoleId = Role::where('name', '=', 'owner')->value('id');
                $managerRoleId = Role::where('name', '=', 'project_manager')->value('id');
                $leadRoleId = Role::where('name', '=', 'team_lead')->value('id');
                $devRoleId = Role::where('name', '=', 'developer')->value('id');

                $pm = Projectroleuser::select(
                    DB::raw('CONCAT(profiles.first_name, " ", profiles.last_name) AS label'),
                    DB::raw('profiles.user_id AS value')
                )
                    ->leftJoin('role_user', 'role_user.user_id', '=', 'projects_used.user_id')
                    ->leftJoin('profiles', 'profiles.user_id', '=', 'projects_used.user_id')
                    ->where('projects_used.project_id', '=', $id)
                    ->where('role_user.role_id', '=', $managerRoleId)
                    ->first();
                $devs = Projectroleuser::select(
                    DB::raw('CONCAT(profiles.first_name, " ", profiles.last_name) AS label'),
                    DB::raw('profiles.user_id AS value')
                )
                    ->leftJoin('role_user', 'role_user.user_id', '=', 'projects_used.user_id')
                    ->leftJoin('profiles', 'profiles.user_id', '=', 'projects_used.user_id')
                    ->where('projects_used.project_id', '=', $id)
                    ->where('role_user.role_id', '!=', $managerRoleId)
                    ->get();

                $tags = Tagsused::select(DB::raw('tags.name as label'), DB::raw('tags.name as value'))
                    ->leftJoin('tags', 'tags.id', '=', 'tags_used.tag_id')
                    ->where('tag_type', '=', 'project')
                    ->where('target_id', '=', $id)
                    ->get();
                $skills = Skillsused::select(DB::raw('skills.name as label'), DB::raw('skills.name as value'))
                    ->leftJoin('skills', 'skills.id', '=', 'skills_used.skill_id')
                    ->where('item_type', '=', 'project')
                    ->where('item_id', '=', $id)
                    ->get();
                $note = Description::select('value')
                    ->where('item_type', '=', 'project')
                    ->where('item_id', '=', $id)
                    ->first();
                return view(
                    'projects.view', [
                        'project' => $project,
                        'project_manager' => $pm,
                        'developers' => $devs,
                        'tags' => $tags,
                        'skills' => $skills,
                        'note' => $note
                    ]
                );
            } else {

                return redirect()->route('project.list')
                    ->withError('Project not found');
            }
        } catch (Exception $e) {
            return redirect()->route('project.list')
                ->withError($e->getCode() . ' ' . $e->getMessage());
        }
    }

    /**
     * Gets own project of an user.
     *
     * @since  03/06/2017
     * @author Renny M Roy <renny.roy@cubettech.com>
     * @return view
     */
    public function getMyProjects()
    {
        $projects = DB::table('projects_used')
            ->where('user_id', Auth::user()->id)
            ->leftJoin('projects', 'projects_used.project_id', '=', 'projects.id')
            ->get();
    }

    /**
     * Gets project Resources
     *
     * @since  12/06/2017
     * @author rameez rami<ramees.pu@cubettech.com>
     * @return view
     */
    public function projectResources($projectId)
    {
        $resources = DB::table('profiles')
            ->select(
                'user_id as id', DB::raw('CONCAT(first_name, " ", last_name) AS title')
            )
            ->join('users', 'profiles.user_id', '=', 'users.id')
            ->where('users.disabled', '=', 0)
            ->where('users.allocatable', '=', 1)
            ->whereExists(
                function ($query) use ($projectId) {
                    $query->select(DB::raw(1))
                        ->from('projects_used')
                        ->whereRaw('profiles.user_id = projects_used.user_id')
                        ->where('projects_used.project_id', '=', $projectId);
                }
            )
            ->get();

        return response()->json(
            [
                'status' => 'success',
                'data' => $resources
            ]
        );
    }

    /**
     * Gets project Events
     *
     * @since  12/06/2017
     * @author rameez rami<ramees.pu@cubettech.com>
     * @return \Illuminate\Http\JsonResponse
     */
    public function projectAllocations(Request $request, $projectId)
    {
        $startDate = $request->input('start');
        $endDate = $request->input('end');

        $allocations = DB::table('allocations as al')
            ->select(
                'al.id', 'al.assignee_id as resourceId', 'al.start_date as start', 'al.end_date as end',
                'al.allocation_value as allocationValue',
                DB::raw('CONCAT(p.first_name, " ", p.last_name, " (",al.allocation_value,"%)") AS title')
            )
            ->where('project_id', '=', $projectId)
            ->join('profiles as p', 'al.assignee_id', '=', 'p.user_id')
            ->where(function ($qry) use ($startDate, $endDate) {
                    $qry->whereRaw("( '" . $startDate . "' BETWEEN DATE(al.start_date) AND DATE(al.end_date) )")
                        ->orWhereRaw("DATE(al.start_date) = '" . $startDate . "'")
                        ->orWhereRaw("DATE(al.end_date) = '" . $startDate . "'");
            })
            ->where(function ($qry) use ($startDate, $endDate) {
                    $qry->whereRaw("( '" . $endDate . "' BETWEEN DATE(al.start_date) AND DATE(al.end_date) )")
                        ->orWhereRaw("DATE(al.start_date) = '" . $endDate . "'")
                        ->orWhereRaw("DATE(al.end_date) = '" . $endDate . "'");
            })
            ->get();

        return response()->json($allocations);
    }

    public function allocateProjectResource(Request $request, $projectId)
    {
        $inputs = $request->all();

        $allocationConflicts = DB::table('allocations as al')
            ->select(
                'p.name', 'al.allocation_value', DB::raw('DATE(al.start_date) as start_date'),
                DB::raw('DATE(al.end_date) as end_date')
            )
            ->join('projects as p', 'al.project_id', '=', 'p.id')
            ->where('assignee_id', '=', $inputs['resource_id'])
            ->whereRaw(" ((DATE(al.start_date) >= '" . $inputs['start_date'] . "' AND DATE(al.start_date) < '" . $inputs['end_date'] . "') OR (DATE(al.end_date) > '" . $inputs['start_date'] . "' AND DATE(al.end_date) <= '" . $inputs['end_date'] . "') OR ('" . $inputs['start_date'] . "' > DATE(al.start_date) AND '" . $inputs['start_date'] . "' < DATE(al.end_date)) OR ('" . $inputs['end_date'] . "' > DATE(al.start_date) AND '" . $inputs['end_date'] . "' < DATE(al.end_date)))")
            ->get();

        if (count($allocationConflicts) > 0) {
            $assignablePercentage = getAllocatablePercentage(
                [
                    'start_date' => $inputs['start_date'],
                    'end_date' => $inputs['end_date'],
                    'resource_id' => $inputs['resource_id']
                ]
            );
        } else {
            $assignablePercentage = 100;
        }

        if ($inputs['allocation'] <= $assignablePercentage) :

            $allocation = new Allocation();
            $allocation->project_id = $projectId;
            $allocation->start_date = $inputs['start_date'];
            $allocation->end_date = $inputs['end_date'];
            $allocation->assignee_id = $inputs['resource_id'];
            $allocation->assigner_id = Auth::user()->id;
            $allocation->allocation_type = 'percentage';
            $allocation->allocation_value = $inputs['allocation'];
            $allocation->save();

            $descriptionNote = trim($inputs['allocation_note']);
            if (strlen($descriptionNote) > 0) {
                $description = new Description();
                $description->item_id = $allocation->id;
                $description->item_type = 'allocation';
                $description->value = $descriptionNote;
                $description->save();
            }

            $jobData = ['allocation_id' => $allocation->id];
            dispatch(new ProjectAllocationMail($jobData));

            return response()->json(
                [
                    'status' => 'success',
                    'data' => []
                ]
            );
        else:
            if ($assignablePercentage <= 0) {
                $txt = 'currently you cannot assign to the selected date range, you need to edit existing allocations first';
            } else {
                $txt = 'you can assign only ' . $assignablePercentage . '% in selected date range';
            }
            $errText = '<strong>Allocation conflicts found(' . $txt . '):</strong>';
            foreach ($allocationConflicts as $key => $allocation) {
                $errText .= sprintf(
                    '<br/>%s: %s allocation found on project %s from %s till %s', ($key + 1),
                    $allocation->allocation_value . "%", '"' . $allocation->name . '"', $allocation->start_date,
                    $allocation->end_date
                );
            }
            return response()->json(
                [
                    'status' => 'error',
                    'message' => $errText
                ]
            );

        endif;
    }

    public function editAllocatedProjectResource(Request $request, $projectId)
    {
        $inputs = $request->all();

        $allocationConflicts = DB::table('allocations as al')
            ->select(
                'p.name', 'al.allocation_value', DB::raw('DATE(al.start_date) as start_date'),
                DB::raw('DATE(al.end_date) as end_date')
            )
            ->join('projects as p', 'al.project_id', '=', 'p.id')
            ->where('al.id', '!=', $inputs['allocation_id'])
            ->where('assignee_id', '=', $inputs['resource_id'])
            ->whereRaw(" ((DATE(al.start_date) >= '" . $inputs['start_date'] . "' AND DATE(al.start_date) < '" . $inputs['end_date'] . "') OR (DATE(al.end_date) > '" . $inputs['start_date'] . "' AND DATE(al.end_date) <= '" . $inputs['end_date'] . "') OR ('" . $inputs['start_date'] . "' > DATE(al.start_date) AND '" . $inputs['start_date'] . "' < DATE(al.end_date)) OR ('" . $inputs['end_date'] . "' > DATE(al.start_date) AND '" . $inputs['end_date'] . "' < DATE(al.end_date)))")
            ->get();

        if (count($allocationConflicts) > 0) {
            $assignablePercentage = getAllocatablePercentage(
                [
                    'start_date' => $inputs['start_date'],
                    'end_date' => $inputs['end_date'],
                    'resource_id' => $inputs['resource_id'],
                    'exclude_allocation_id' => $inputs['allocation_id']
                ]
            );
        } else {
            $assignablePercentage = 100;
        }


        if ($inputs['allocation'] <= $assignablePercentage) :
            $allocation = Allocation::where('id', '=', $inputs['allocation_id'])->first();


            /* checking if data changed - starts*/

            $changeCheckValues = [
                'start_date' => [
                    'existing' => Carbon::parse($allocation->start_date)->toDateString(),
                    'changed' => $inputs['start_date']
                ],
                'end_date' => [
                    'existing' => Carbon::parse($allocation->end_date)->toDateString(),
                    'changed' => $inputs['end_date']
                ],
                'allocation_value' => ['existing' => $allocation->allocation_value, 'changed' => $inputs['allocation']],
                'description' => [
                    'existing' => $allocation->present()->description,
                    'changed' => $inputs['allocation_note']
                ]
            ];

            $modified = false;
            foreach ($changeCheckValues as $changeCheckValue) {
                if ($changeCheckValue['existing'] != $changeCheckValue['changed']) {
                    $modified = true;
                }
            }

            if ($modified == false) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'No changes Found'
                    ]
                );
            }

            /* checking if data changed - ends*/

            if (count($allocation) > 0) {
                $allocation->project_id = $projectId;
                $allocation->start_date = $inputs['start_date'];
                $allocation->end_date = $inputs['end_date'];
                //$allocation->assignee_id = $inputs['resource_id'];
                $allocation->assigner_id = Auth::user()->id;
                $allocation->allocation_type = 'percentage';
                $allocation->allocation_value = $inputs['allocation'];
                $allocation->update();

                $descriptionNote = trim($inputs['allocation_note']);
                if (strlen($descriptionNote) > 0) {
                    $description = Description::where('item_id', '=', $allocation->id)
                        ->where('item_type', '=', 'allocation')
                        ->first();
                    if (count($description) > 0) {
                        $description->value = $inputs['allocation_note'];
                        $description->update();
                    } else {
                        $descriptionNote = trim($inputs['allocation_note']);
                        if (strlen($descriptionNote) > 0) {
                            $description = new Description();
                            $description->item_id = $allocation->id;
                            $description->item_type = 'allocation';
                            $description->value = $descriptionNote;
                            $description->save();
                        }
                    }
                } else {
                    Description::where('item_id', '=', $allocation->id)
                        ->where('item_type', '=', 'allocation')
                        ->delete();
                }

                $jobData = [
                    'allocation_id' => $allocation->id,
                    'changeCheckValues' => $changeCheckValues
                ];

                dispatch(new EditedProjectAllocationMail($jobData));
            }

            return response()->json(
                [
                    'status' => 'success',
                    'data' => $allocation
                ]
            );
        else:
            $errText = '<strong>Allocation conflicts found(you can assign only ' . $assignablePercentage . '% in selected date range):</strong>';
            foreach ($allocationConflicts as $key => $allocation) {
                $errText .= sprintf(
                    '<br/>%s: %s allocation found on project %s from %s till %s', ($key + 1),
                    $allocation->allocation_value . "%", '"' . $allocation->name . '"', $allocation->start_date,
                    $allocation->end_date
                );
            }
            return response()->json(
                [
                    'status' => 'error',
                    'message' => $errText
                ]
            );

        endif;
    }

    public function deleteAllocatedProjectResource(Request $request)
    {
        $inputs = $request->all();

        $allocation = Allocation::find($inputs['allocation_id']);

        $jobData = [
            'project_name' => $allocation->project->name,
            'allocation' => $allocation->allocation_value,
            'assignee_name' => $allocation->assigneeProfile->first_name,
            'assignee_mail' => $allocation->assignee->email,
            'assigner_name' => $allocation->assignerProfile->first_name,
            'end_date' => $allocation->start_date,
            'start_date' => $allocation->end_date
        ];

        $allocation->delete();

        dispatch(new DeleteProjectAllocationMail($jobData));


        return response()->json(
            [
                'status' => 'success',
                'data' => []
            ]
        );
    }

    public function getAllocationDescription($allocationId)
    {
        $description = DB::table('descriptions')
            ->where('item_id', '=', $allocationId)
            ->where('item_type', '=', 'allocation')
            ->value('value');

        return response()->json(
            [
                'status' => 'success',
                'data' => $description
            ]
        );
    }

    function mail()
    {

        $templateData = [
            'firstName' => 'fname',
            'appName' => appName(),
            'appUrl' => appUrl(),
            'email' => 'sdaskj@sd.com',
            'password' => 'pp123'
        ];
        $mailData = [
            'to' => 'ramees.pu@cubettech.com',
            'name' => 'rameez',
            'subject' => 'test mail'
        ];

        Mail::queue(
            'emails.resource-invite', $templateData, function ($m) use ($mailData) {
            $m->to($mailData['to'], $mailData['name'])
                ->subject($mailData['subject']);
        }
        );

        die;
    }

    function test()
    {


        $templateData = [
            'firstName' => 'fname',
            'appName' => appName(),
            'appUrl' => appUrl(),
            'email' => 'sdaskj@sd.com',
            'password' => 'pp123'
        ];
        $mailData = [
            'to' => 'ramees.pu@cubettech.com',
            'name' => 'rameez',
            'subject' => 'test mail'
        ];

        Mail::queue(
            'emails.resource-invite', $templateData, function ($m) use ($mailData) {
            $m->to($mailData['to'], $mailData['name'])
                ->subject($mailData['subject']);
        }
        );

        die;
    }

    function ajaxFilter(Request $request)
    {
        $project = $request->input('pname');
        $skills = $request->input('skills');

        $query = Project::select('*');
        $paginate = 20;

        if ($project != '') {
            $query->Where("name", "LIKE", "%{$project}%");
        }

        if (count($skills)) {
            $query->whereExists(
                function ($query) use ($skills) {
                    $query->select(DB::raw(1))
                        ->from('skills_used')
                        ->whereRaw('skills_used.item_id = projects.id')
                        ->where('skills_used.item_type', '=', 'project')
                        ->whereIn('skill_id', $skills);
                }
            );
        }

        $query->orderBy('id', 'DESC');
        $projects = $query->paginate($paginate);

        $html = view('projects.partials.project-search-result-block', ['projects' => $projects])->render();

        if ($request->ajax()) {
            return response()->json(
                [
                    'status' => 'success',
                    'html' => $html,
                ]
            );
        } else {
            $skills = Skill::all();
            $search = $request->input('pname');
            $inputSkills = $request->input('skills') ? $request->input('skills') : [];
            return view('projects.index', compact('projects', 'skills', 'search', 'inputSkills'))->with('i',
                ($request->input('page', 1) - 1) * $paginate);
        }


    }
}
