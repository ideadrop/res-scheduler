<?php namespace App\Http\Controllers;

use App\Allocation;
use Illuminate\Http\Request;
use DB;
use App\Project;
use App\User;
use App\Skill;
use Carbon\Carbon;
use Auth;
use App\Tag;

class ReportController extends Controller
{

    /**
     * Project Based Report
     *
     * @since  2017-07-18
     * @author rameez rami <ramees.pu@cubettech.com>
     * @return view
     */
    public function projectBasedReport()
    {
        return view('reports.project-based');
    }
    /**
     * Fetch/FIlter Project Based Report
     *
     * @since  2017-07-18
     * @author rameez rami <ramees.pu@cubettech.com>
     * @return view
     */
    public function fetchProjectBasedReport(Request $request)
    {

        $inputs = $request->all();
        $search = $inputs['report_project_search'];
        $start = $inputs['report_start_date'];
        $end = $inputs['report_end_date'];

        $page = (isset($inputs['page']))?$inputs['page']:0;
        $take = 5;
        $skip = ($page*$take);

        $query  = Project::select('*');

        if($search!='') {
            $query->where(
                function ($qry) use ($search) {
                    $qry->where("name", "LIKE", "%{$search}%")
                        ->orWhere("project_code", "LIKE", "%{$search}%");
                }
            );
        }

        if($start != '' && $end !='') {
            $start = "'".$start."'";
            $end = "'".$end."'";
            $query->where("start_date", "<>", "")
                ->where("end_date", "<>", "")
                ->whereRaw("DATE(start_date) <= $end")
                ->whereRaw("DATE(end_date) >= $start");
                //->whereRaw("DATE(end_date) <> $start")
                //->whereRaw("DATE(end_date) <> $end");
        }

        $query->skip($skip)
            ->take($take)
            ->orderBy('id', 'DESC');

        $projects= $query->get();

        $html = view('reports.partials.project-based-report-card', ['projects'=>$projects])->render();

        $loadmore = (isset($inputs['loadmore']))?$inputs['loadmore']:0;
        if($loadmore !=1 && count($projects)==0) {
            $html = '<p>No results found</p>';
        }

        return response()->json(
            [
            'status' => 'success',
            'html' => $html,
            'data' => [
                'next_page' => (count($projects)>0)?(intval($page)+1):'end'
            ]
            ]
        );
    }

    /**
     * Export Project Based Report
     *
     * @since  2017-07-18
     * @author rameez rami <ramees.pu@cubettech.com>
     * @return view
     */
    public function exportProjectBasedReport(Request $request)
    {

        $inputs = $request->all();

        $search = $inputs['report_project_search'];
        $start = $inputs['report_start_date'];
        $end = $inputs['report_end_date'];


        $query  = Project::select('*');

        if($search!='') {
            $query->where(
                function ($qry) use ($search) {
                    $qry->where("name", "LIKE", "%{$search}%")
                        ->orWhere("project_code", "LIKE", "%{$search}%");
                }
            );
        }

        if($start != '' && $end !='') {
            $start = "'".$start."'";
            $end = "'".$end."'";
            $query->where("start_date", "<>", "")
                ->where("end_date", "<>", "")
                ->whereRaw("DATE(start_date) <= $end")
                ->whereRaw("DATE(end_date) >= $start");
            /*$query->where("start_date","<>","")
                ->where("end_date","<>","")
                ->whereRaw("DATE(start_date) <= $end")
                ->whereRaw("DATE(end_date) >= $start")
                ->whereRaw("DATE(end_date) <> $start")
                ->whereRaw("DATE(end_date) <> $end");*/
        }

        $query->orderBy('id', 'DESC');

        $projects = $query->get();

        $delimiter = ',';
        $filename = "project_based_report_" . time() . ".csv";
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=\"$filename\"");

        $out = fopen("php://output", 'w');

        // Print the Organisation and challenge details to the CSV Headers
        $this->insertCsvBlankRow($out);

        /*####### CSV FILTER DATA STARTS ##############*/
        $filterData = [
            ['Search: '.$search]
        ];
        if($start != '' && $end !='') {
            $filterData[] = ['Start Range:'.$start];
            $filterData[] = ['End Range:'.$end];
        }
        foreach ($filterData as $sKey => $filter) {
            fputcsv($out, $filter, $delimiter, '"');
        }
        /*####### CSV FILTER DATA ENDS ##############*/

        $this->insertCsvBlankRow($out);
        $this->insertCsvBlankRow($out);


        foreach ($projects as $key => $project):

            /* Single project details starts */
            $this->insertCsvBlankRow($out);
            fputcsv($out, [strtoupper($project->name)], $delimiter, '"');
            $this->insertCsvSeparatorRow($out);
            $printData = [
                ['PROJECT NAME : '.$project->name],
                ['PROJECT CODE: '.$project->project_code],
                ['DESCRIPTION: '.$project->present()->description()],
                ['START DATE: '.formatAllocationDate($project->start_date)],
                ['END DATE: '.formatAllocationDate($project->end_date)]
            ];
            foreach ($printData as $sKey => $print) {
                fputcsv($out, $print, $delimiter, '"');
            }
            /* Single project details ends */
            foreach($project->resources as $resource):
                    $resourceUser = $resource->user;
                    $resourceProfile = $resourceUser->profile;
                    $resourceAllocations = $resourceUser->allocations($project->id)->get();
                $this->insertCsvBlankRow($out);
                fputcsv($out, [strtoupper($resourceProfile->first_name.' '.$resourceProfile->last_name)], $delimiter, '"');
                $this->insertCsvLineRow($out);

                if(count($resourceAllocations)>0) {
                    fputcsv($out, ['START DATE','END DATE','ALLOCATION %'], $delimiter, '"');
                }else{
                    fputcsv($out, ['No allocations found for this project'], $delimiter, '"');
                }
                foreach ($resourceAllocations as $allocation) {
                    $csvData = [
                        formatAllocationDate($allocation->start_date),
                        formatAllocationDate($allocation->end_date),
                        $allocation->allocation_value
                    ];
                    fputcsv($out, $csvData, $delimiter, '"');
                }

            endforeach;


        endforeach;

        fclose($out);

        die;
    }
    public function insertCsvBlankRow($out)
    {
        return fputcsv($out, [''], ',', '"');
    }
    public function insertCsvSeparatorRow($out)
    {
        return fputcsv($out, ['****************************************************'], ',', '"');
    }
    public function insertCsvLineRow($out)
    {
        return fputcsv($out, ['--------------------------------'], ',', '"');
    }
    /**
     * User Based Report
     *
     * @since  2017-07-18
     * @author rameez rami <ramees.pu@cubettech.com>
     * @return view
     */
    public function userBasedReport(Request $request)
    {
        $fillSearch = trim($request->get('report_user_search'));
        $fillSkill = (is_array($request->get('skills')))?$request->get('skills'):[];
        if($fillSearch!='' || count($fillSkill)>0) {

            return redirect()
                ->route('reports.user.based')
                ->with('fill_skill', $fillSkill)
                ->with('fill_search', $fillSearch);

        }

        $fillSearch = '';
        $fillSkill = [];
        if ($request->session()->has('fill_search')) {
            $fillSearch = session('fill_search');
        }
        if ($request->session()->has('fill_skill')) {
            $fillSkill = session('fill_skill');
        }

        $skills = Skill::all();

        return view(
            'reports.user-based', [
            'skills'=>$skills,
            'fillSearch'=>$fillSearch,
            'fillSkill'=>$fillSkill
            ]
        );
    }

    public function userBasedReportCalender(Request $request)
    {
        $fillSearch = trim($request->get('report_user_search'));
        $fillSkill = (is_array($request->get('skills')))?$request->get('skills'):[];
        if($fillSearch!='' || count($fillSkill)>0) {

            return redirect()
                ->route('reports.user.based.calender')
                ->with('fill_skill', $fillSkill)
                ->with('fill_search', $fillSearch);

        }


        $fillSearch = '';
        $fillSkill = [];
        if ($request->session()->has('fill_search')) {
            $fillSearch = session('fill_search');
        }
        if ($request->session()->has('fill_skill')) {
            $fillSkill = session('fill_skill');
        }
        $skills = Skill::all();

        return view(
            'reports.user-based-calender', [
            'skills'=>$skills,
            'fillSearch'=>$fillSearch,
            'fillSkill'=>$fillSkill
            ]
        );
    }
    /**
     * Gets project Resources
     *
     * @since  12/06/2017
     * @author rameez rami<ramees.pu@cubettech.com>
     * @return view
     */
    public function userBasedReportResources(Request $request)
    {
        $inputs = $request->all();
        $search = trim($inputs['search']);
        $skills = (is_array($inputs['skills']))?$inputs['skills']:[];


        $query = DB::table('profiles')
            ->select(
                'user_id as id',
                DB::raw('CONCAT(first_name, " ", last_name) AS title')
            )
            ->join('users as u', 'profiles.user_id', '=', 'u.id');
        if($search!='') {
            $query->where(
                function ($qry) use ($search) {
                    $qry->where(DB::raw('CONCAT(profiles.first_name, " ", profiles.last_name)'), "LIKE", "%{$search}%")
                        ->orWhere("u.email", "LIKE", "%{$search}%");
                }
            );
        }
        if(count($skills)>0) {
            $query->whereExists(
                function ($query) use ($skills) {
                    $query->select(DB::raw(1))
                        ->from('skills_used')
                        ->whereRaw('skills_used.item_id = profiles.user_id')
                        ->where('skills_used.item_type', '=', 'user')
                        ->whereIn('skill_id', $skills);
                }
            );
        }
        /*$query->whereExists(function ($query){
            $query->select(DB::raw(1))
                ->from('projects_used')
                ->whereRaw('profiles.user_id = projects_used.user_id');
        });*/

        $resources = $query->orderBy('id', 'DESC')->get();

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
    public function userBasedReportAllocations(Request $request)
    {
        $inputs = $request->all();
        $startDate = $inputs['start'];
        $endDate = $inputs['end'];
        $skills = (is_array($inputs['skills']))?$inputs['skills']:[];


        $search = (isset($inputs['report_user_search'])?$inputs['report_user_search']:'');

        $query = DB::table('allocations as al')
            ->select(
                'al.id',
                'al.assignee_id as resourceId',
                'al.start_date as start',
                'al.end_date as end',
                'al.allocation_value as allocationValue',
                'p.name AS title'
            );
        $query->join('projects as p', 'al.project_id', '=', 'p.id');

        if($search!='') {
            $query->join('profiles as pf', 'al.assignee_id', '=', 'pf.user_id')
                ->join('user as u', 'al.assignee_id', '=', 'u.id');
        }


        $query->where(
            function ($qry) use ($startDate, $endDate) {
                $qry->whereRaw("( DATE(al.start_date) >= '" . $startDate . "' AND DATE(al.start_date) <= '" . $endDate . "' )")
                    ->orWhereRaw("( DATE(al.end_date) >= '" . $startDate . "' AND DATE(al.end_date) <= '" . $endDate . "' )");
            }
        );



        if($search!='') {
            $query->where(
                function ($qry) use ($search) {
                    $qry->where(DB::raw('CONCAT(pf.first_name, " ", pf.last_name)'), "LIKE", "%{$search}%")
                        ->orWhere("u.email", "LIKE", "%{$search}%");
                }
            );

        }
        $allocations = $query->get();

        return response()->json(
            [
            'status' => 'success',
            'data' => $allocations
            ]
        );
    }

    /**
     * User Based Report
     *
     * @since  2017-07-18
     * @author rameez rami <ramees.pu@cubettech.com>
     * @return view
     */
    public function fetchUserBasedReport(Request $request)
    {

        $inputs = $request->all();
        $search = addslashes($inputs['report_user_search']);


        $skills = (isset($inputs['skills']))?$inputs['skills']:[];

        $page = (isset($inputs['page']))?$inputs['page']:0;
        $take = 5;
        $skip = ($page*$take);

        $query = User::select(
            'users.*',
            'p.first_name',
            'p.last_name'
        )
        ->join('profiles as p', 'users.id', '=', 'p.user_id')
        ->where('users.disabled','=',0);
        if($search!='') {
            $query->where(
                function ($qry) use ($search) {
                    $qry->where(DB::raw('CONCAT(p.first_name, " ", p.last_name)'), "LIKE", "%{$search}%")
                        ->orWhere("email", "LIKE", "%{$search}%");
                }
            );
        }

        if(count($skills)>0) {
            $query->whereExists(
                function ($query) use ($skills) {
                    $query->select(DB::raw(1))
                        ->from('skills_used')
                        ->whereRaw('skills_used.item_id = users.id')
                        ->where('skills_used.item_type', '=', 'user')
                        ->whereIn('skill_id', $skills);
                }
            );
        }

        $query->skip($skip)->take($take)->orderBy('id', 'DESC');

        $resourceUsers = $query->get();

        $html = view('reports.partials.user-based-report-card', ['resourceUsers'=>$resourceUsers])->render();

        $loadmore = (isset($inputs['loadmore']))?$inputs['loadmore']:0;
        if($loadmore !=1 && count($resourceUsers)==0) {
            $html = '<p>No results found</p>';
        }

        return response()->json(
            [
            'status' => 'success',
            'html' => $html,
            'data' => [
                'next_page' => (count($resourceUsers)>0)?(intval($page)+1):'end'
            ]
            ]
        );
    }

    /**
     * Export User Based Report
     *
     * @param  Request $request
     * @since  2017-07-21
     * @author rameez rami <ramees.pu@cubettech.com>
     */
    public function exportUserBasedReport(Request $request)
    {

        $inputs = $request->all();

        $search = $inputs['report_user_search'];

        $skills = (isset($inputs['skills']))?$inputs['skills']:[];

        $start = (isset($inputs['start']))?$inputs['start']:'';
        $end = (isset($inputs['end']))?$inputs['end']:'';

        $query = User::select(
            'users.*',
            'p.first_name',
            'p.last_name'
        )
            ->join('profiles as p', 'users.id', '=', 'p.user_id');

        if($search!='') {
            $query->where(
                function ($qry) use ($search) {
                    $qry->where(DB::raw('CONCAT(p.first_name, " ", p.last_name)'), "LIKE", "%{$search}%")
                        ->orWhere("email", "LIKE", "%{$search}%");
                }
            );
        }

        if(count($skills)>0) {
            $query->whereExists(
                function ($query) use ($skills) {
                    $query->select(DB::raw(1))
                        ->from('skills_used')
                        ->whereRaw('skills_used.item_id = users.id')
                        ->where('skills_used.item_type', '=', 'user')
                        ->whereIn('skill_id', $skills);
                }
            );
        }


        $resourceUsers = $query->get();

        $delimiter = ',';
        $filename = "user_based_report_" . time() . ".csv";
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=\"$filename\"");

        $out = fopen("php://output", 'w');

        // Print the Organisation and challenge details to the CSV Headers
        $this->insertCsvBlankRow($out);

        /*####### CSV FILTER DATA STARTS ##############*/
        $filterData = [
            ['Search: '.$search]
        ];
        foreach ($filterData as $sKey => $filter) {
            fputcsv($out, $filter, $delimiter, '"');
        }
        /*####### CSV FILTER DATA ENDS ##############*/

        $this->insertCsvBlankRow($out);
        $this->insertCsvBlankRow($out);
        $this->insertCsvBlankRow($out);


        foreach ($resourceUsers as $key => $resourceUser):

            /* Single project details starts */
            fputcsv($out, [strtoupper($resourceUser->first_name.' '.$resourceUser->last_name)], $delimiter, '"');
            $this->insertCsvSeparatorRow($out);
            $printData = [
                ['FIRST NAME : '.$resourceUser->first_name],
                ['LAST NAME: '.$resourceUser->last_name],
                ['EMAIL: '.$resourceUser->email],
                ['DESIGNATION: '.$resourceUser->present()->designation]
            ];
            foreach ($printData as $sKey => $print) {
                fputcsv($out, $print, $delimiter, '"');
            }
            /* Single project details ends */
            if(count($resourceUser->assignedProjects)==0) {
                fputcsv($out, ['No project found for this resource'], $delimiter, '"');
            }
            foreach($resourceUser->assignedProjects as $assignedProject):
                $project = $assignedProject->project;
                $resourceAllocations = $resourceUser->allocations($project->id)->get();
                $this->insertCsvBlankRow($out);
                fputcsv($out, [strtoupper($project->name)], $delimiter, '"');
                $this->insertCsvLineRow($out);

                if(count($resourceAllocations)>0) {
                    fputcsv($out, ['START DATE','END DATE','ALLOCATION %'], $delimiter, '"');
                }else{
                    fputcsv($out, ['No allocations found for this project'], $delimiter, '"');
                }
                foreach ($resourceAllocations as $allocation) {
                    $csvData = [
                        formatAllocationDate($allocation->start_date),
                        formatAllocationDate($allocation->end_date),
                        $allocation->allocation_value
                    ];
                    fputcsv($out, $csvData, $delimiter, '"');
                }

            endforeach;


        endforeach;

        fclose($out);

        die;
    }
    /**
     * Skill Based Report
     *
     * @since  2017-07-18
     * @author rameez rami <ramees.pu@cubettech.com>
     * @return view
     */
    public function skillBasedReport()
    {
        return view('reports.skill-based');
    }

    /**
     * Fetch Skill Based Report
     *
     * @since  2017-07-18
     * @author rameez rami <ramees.pu@cubettech.com>
     * @return view
     */
    public function fetchSkillBasedReport(Request $request)
    {

        $inputs = $request->all();
        $search = addslashes($inputs['report_skill_search']);

        $page = (isset($inputs['page']))?$inputs['page']:0;

        $take = 5;
        $skip = ($page*$take);

        $query = Skill::select(DB::raw('skills.*, (SELECT COUNT(su.id) FROM skills_used su WHERE su.skill_id = skills.id) AS usedCount'));


        if($search!='') {
            $query->where("name", "LIKE", "%{$search}%");
        }

        $query->skip($skip)->take($take)->orderBy('usedCount', 'DESC');

        $skills = $query->get();

        $html = view('reports.partials.skill-based-report-card', ['skills'=>$skills])->render();

        $loadmore = (isset($inputs['loadmore']))?$inputs['loadmore']:0;
        if($loadmore !=1 && count($skills)==0) {
            $html = '<p>No results found</p>';
        }

        return response()->json(
            [
            'status' => 'success',
            'html' => $html,
            'data' => [
                'next_page' => (count($skills)>0)?(intval($page)+1):'end'
            ]
            ]
        );
    }

    /**
     * Export Skill Based Report
     *
     * @param  Request $request
     * @since  2017-07-21
     * @author rameez rami <ramees.pu@cubettech.com>
     */
    public function exportSkillBasedReport(Request $request)
    {

        $inputs = $request->all();

        $search = $inputs['report_skill_search'];

        $query = Skill::select(DB::raw('skills.*, (SELECT COUNT(su.id) FROM skills_used su WHERE su.skill_id = skills.id) AS usedCount'));

        if ($search != '') {
            $query->where("name", "LIKE", "%{$search}%");
        }

        $query->orderBy('usedCount', 'DESC');

        $skills = $query->get();

        $delimiter = ',';
        $filename = "skill_based_report_" . time() . ".csv";
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=\"$filename\"");

        $out = fopen("php://output", 'w');

        // Print the Organisation and challenge details to the CSV Headers
        $this->insertCsvBlankRow($out);

        /*####### CSV FILTER DATA STARTS ##############*/
        $filterData = [
            ['Search: ' . $search]
        ];
        foreach ($filterData as $sKey => $filter) {
            fputcsv($out, $filter, $delimiter, '"');
        }
        /*####### CSV FILTER DATA ENDS ##############*/

        $this->insertCsvBlankRow($out);


        foreach ($skills as $key => $skill):
            $usedProjects = $skill->usedProjects;
            $usedProjectsCount = count($usedProjects);

            $usedResources = $skill->usedResources;
            $usedResourcesCount = count($usedResources);



            /* Single project details starts */
            $this->insertCsvBlankRow($out);
            fputcsv($out, [strtoupper($skill->name)], $delimiter, '"');
            $this->insertCsvSeparatorRow($out);


            if ($usedProjectsCount == 0 && $usedResourcesCount == 0) {
                fputcsv($out, ['No Projects/Resources found against this skill'], $delimiter, '"');
            }

            if ($usedProjectsCount > 0) {
                $this->insertCsvBlankRow($out);
                fputcsv($out, ['PROJECTS'], $delimiter, '"');
                $this->insertCsvBlankRow($out);
                fputcsv($out, ['PROJECT NAME','PROJECT CODE','START DATE','END DATE','DESCRIPTION'], $delimiter, '"');

            }

            foreach($usedProjects as $usedProject):
                $project = $usedProject->project;
                $csvData = [
                    $project->name,
                    $project->project_code,
                    formatAllocationDate($project->start_date),
                    formatAllocationDate($project->end_date),
                    $project->present()->description()
                ];
                fputcsv($out, $csvData, $delimiter, '"');
            endforeach;
            if ($usedResourcesCount > 0) {
                $this->insertCsvBlankRow($out);
                fputcsv($out, ['RESOURCES'], $delimiter, '"');
                $this->insertCsvBlankRow($out);
                fputcsv($out, ['FIRST NAME','LAST NAME','EMAIL','DESIGNATION'], $delimiter, '"');
            }
            foreach($usedResources as $usedResource):
                $resource = $usedResource->resource;
                $resourceProfile = $resource->profile;

                $csvData = [
                    $resourceProfile->first_name,
                    $resourceProfile->last_name,
                    $resource->email,
                    $resource->present()->designation
                ];
                fputcsv($out, $csvData, $delimiter, '"');
            endforeach;


        endforeach;

        fclose($out);

        die;
    }
    /**
     * Tag Based Report
     *
     * @since  2017-07-18
     * @author rameez rami <ramees.pu@cubettech.com>
     * @return view
     */
    public function tagBasedReport()
    {
        return view('reports.tag-based');
    }

    /**
     * Fetch Tag User Based Report
     *
     * @since  2017-07-18
     * @author rameez rami <ramees.pu@cubettech.com>
     * @return view
     */
    public function fetchTagBasedReport(Request $request)
    {

        $inputs = $request->all();
        $search = addslashes($inputs['report_tag_search']);

        $page = (isset($inputs['page']))?$inputs['page']:0;

        $take = 5;
        $skip = ($page*$take);

        $query = Tag::select(DB::raw('tags.*, (SELECT COUNT(tu.id) FROM tags_used tu WHERE tu.tag_id = tags.id) AS usedCount'));


        if($search!='') {
            $query->where("name", "LIKE", "%{$search}%");
        }

        $query->skip($skip)->take($take)->orderBy('usedCount', 'DESC');

        $tags = $query->get();

        $html = view('reports.partials.tag-based-report-card', ['tags'=>$tags])->render();

        $loadmore = (isset($inputs['loadmore']))?$inputs['loadmore']:0;
        if($loadmore !=1 && count($tags)==0) {
            $html = '<p>No results found</p>';
        }

        return response()->json(
            [
            'status' => 'success',
            'html' => $html,
            'data' => [
                'next_page' => (count($tags)>0)?(intval($page)+1):'end'
            ]
            ]
        );
    }


    /**
     * Export Tag Based Report
     *
     * @param  Request $request
     * @since  2017-07-21
     * @author rameez rami <ramees.pu@cubettech.com>
     */
    public function exportTagBasedReport(Request $request)
    {

        $inputs = $request->all();

        $search = $inputs['report_tag_search'];

        $query = Tag::select(DB::raw('tags.*, (SELECT COUNT(tu.id) FROM tags_used tu WHERE tu.tag_id = tags.id AND tu.tag_type = "project" ) AS usedCount'));

        if ($search != '') {
            $query->where("name", "LIKE", "%{$search}%");
        }

        $query->orderBy('usedCount', 'DESC');

        $tags = $query->get();

        $delimiter = ',';
        $filename = "tag_based_report_" . time() . ".csv";
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=\"$filename\"");

        $out = fopen("php://output", 'w');

        // Print the Organisation and challenge details to the CSV Headers
        $this->insertCsvBlankRow($out);

        /*####### CSV FILTER DATA STARTS ##############*/
        if($search!='') {
            $filterData = [
                ['Search: ' . $search]
            ];
            foreach ($filterData as $sKey => $filter) {
                fputcsv($out, $filter, $delimiter, '"');
            }
        }
        /*####### CSV FILTER DATA ENDS ##############*/

        $this->insertCsvBlankRow($out);


        foreach ($tags as $key => $tag):

            $taggedProjects = $tag->getTaggedProjects;
            $taggedProjectsCount = count($taggedProjects);

            $taggedResources = $tag->getTaggedResources;
            $taggedResourcesCount = count($taggedResources);

            if ($taggedProjectsCount == 0 && $taggedResourcesCount == 0) {
                fputcsv($out, ['No Projects/Resources found against this tag'], $delimiter, '"');
            }

            /* Single project details starts */
            $this->insertCsvBlankRow($out);
            fputcsv($out, [strtoupper($tag->name)], $delimiter, '"');
            $this->insertCsvSeparatorRow($out);

            if ($taggedProjectsCount > 0) {
                $this->insertCsvBlankRow($out);
                fputcsv($out, ['PROJECTS'], $delimiter, '"');
                $this->insertCsvBlankRow($out);
                fputcsv($out, ['PROJECT NAME','PROJECT CODE','START DATE','END DATE','DESCRIPTION'], $delimiter, '"');

            }

            foreach($taggedProjects as $taggedProject):
                $project = $taggedProject->project;
                $csvData = [
                    $project->name,
                    $project->project_code,
                    formatAllocationDate($project->start_date),
                    formatAllocationDate($project->end_date),
                    $project->present()->description()
                ];
                fputcsv($out, $csvData, $delimiter, '"');
            endforeach;
            if ($taggedResourcesCount > 0) {
                $this->insertCsvBlankRow($out);
                fputcsv($out, ['RESOURCES'], $delimiter, '"');
                $this->insertCsvBlankRow($out);
                fputcsv($out, ['FIRST NAME','LAST NAME','EMAIL','DESIGNATION'], $delimiter, '"');
            }
            foreach($taggedResources as $taggedResource):
                $resource = $taggedResource->resource;
                $resourceProfile = $resource->profile;

                $csvData = [
                    $resourceProfile->first_name,
                    $resourceProfile->last_name,
                    $resource->email,
                    $resource->present()->designation
                ];
                fputcsv($out, $csvData, $delimiter, '"');
            endforeach;


        endforeach;

        fclose($out);

        die;
    }

}
