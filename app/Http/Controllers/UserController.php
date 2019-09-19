<?php namespace App\Http\Controllers;

use App\Allocation;
use App\Description;
use App\Http\Requests;
use App\Profile;
use App\Project;
use App\Projectroleuser;
use App\Role;
use App\Skill;
use App\Skillsused;
use App\User;
use App\Usertype;
use Auth;
use Carbon\Carbon;
use DB;
use finfo;
use Hash;
use Illuminate\Http\Request;
use Mail;
use Validator;

class UserController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $days = 20;
        $users = User::orderBy('id', 'DESC')->paginate($days);
        $skills = Skill::all();
        $roles = Role::all();
        return view('users.index', compact('users', 'skills', 'roles'))
            ->with('i', ($request->input('page', 1) - 1) * $days);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::lists('display_name', 'id');
        $userTypes = Usertype::lists('name', 'id');

        return view('users.create', compact('roles', 'userTypes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $messages = [
            'allocatable.required' => 'Please select allocatable or not.',
            'role.required' => 'Please select a role in the company.',
            'user_type_id.required' => 'Please select a Designation.',
            'first_name.required' => 'Please add Firstname.',
            'last_name.required' => 'Please add Lastname.',
            'address_line1.required' => 'Please add Address Line 1.',
            'phone.required' => 'Please add Phone number.',
        ];
        $this->validate(
            $request, [
            'email' => 'required|email|unique:users,email',
            'allocatable' => 'required',
            'password' => 'required|same:confirm-password',
            'role' => 'required',
            'user_type_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'company' => 'required',
            'address_line1' => 'required',
            'phone' => 'required',
        ], $messages
        );

        $validator = Validator::make(
            $request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'allocatable' => 'required',
            'role' => 'required',
            'user_type_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'company' => 'required',
            'address_line1' => 'required',
            'phone' => 'required',
        ], $messages
        );

        if ($validator->fails()) {
            return redirect()->route('resources.create')
                ->withErrors($validator)
                ->withInput();
        }

        $input = $request->all();
        $input['first_name'] = trim($input['first_name']);
        $input['last_name'] = trim($input['last_name']);
        $input['email'] = trim($input['email']);
        $plainPassword = $input['password'];
        $input['password'] = Hash::make($input['password']);

        DB::beginTransaction();
        try {
            $user = User::create(
                [
                    'email' => $input['email'],
                    'password' => $input['password'],
                    'allocatable' => $input['allocatable']
                ]
            );

            DB::table('role_user')->insert(['user_id' => $user->id, 'role_id' => $request->input('role')]);

            $userProfile = Profile:: create(
                [
                    'user_id' => $user->id,
                    'first_name' => $input['first_name'],
                    'last_name' => $input['last_name'],
                    'company' => $input['company'],
                    'designation' => $input['user_type_id'],
                    'address_line1' => $input['address_line1'],
                    'address_line2' => $input['address_line2'],
                    'phone' => $input['phone'],
                    'city' => $input['city'],
                    'state' => $input['state'],
                    'country' => $input['country'],
                    'zipcode' => $input['zipcode']
                ]
            );

            //Manage Skills

            $skillsArray = commaToArray($input['user-skills']);

            foreach ($skillsArray as $sKey => $skill) {
                if (str_replace(" ", "", $skill) != "") {
                    $skillExists = Skill::where('name', '=', trim($skill))->first();

                    if (!is_null($skillExists)) {
                        Skillsused::create(
                            [
                                'item_type' => 'user',
                                'skill_id' => $skillExists->id,
                                'item_id' => $user->id
                            ]
                        );
                    } else {
                        //Insert into skills table
                        $skillObj = Skill::create(['name' => trim($skill), 'author_id' => Auth::user()->id]);
                        //Insert into skills_used table
                        Skillsused::create(
                            [
                                'item_type' => 'user',
                                'skill_id' => $skillObj->id,
                                'item_id' => $user->id
                            ]
                        );
                    }
                }
            }

            DB::commit();


            /*QUEUE A EMAIL NOTIFICATION STARTS*/
            $templateData = [
                'firstName' => $userProfile->first_name,
                'appName' => appName(),
                'appUrl' => appUrl(),
                'email' => $user->email,
                'password' => $plainPassword
            ];
            $mailData = [
                'to' => $user->email,
                'name' => $userProfile->first_name,
                'subject' => 'You have Been Invited To ' . appName()
            ];

            Mail::queue(
                'emails.resource-invite', $templateData, function ($m) use ($mailData) {
                $m->to($mailData['to'], $mailData['name'])
                    ->subject($mailData['subject']);
            }
            );
            /*QUEUE A EMAIL NOTIFICATION ENDS*/

            return redirect()->route('resources.index')
                ->with('success', 'Resource added successfully');
        } catch (\Exception $e) {
            //rollback transaction if exception occurred while saving
            DB::rollback();
            return redirect()->route('resources.create')
                ->with('error', $e->getLine() . " " . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $authUser = Auth::user();
        if ((!$authUser->can(['resource-list-allocate'])) && ($authUser->can(['view-my-project-calender'])) && ($id != $authUser->id)) :
            return view('errors.403');
        endif;

        $user = User::with('profile')->find($id);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::with('profile')->find($id);
        $roles = Role::lists('display_name', 'id');
        $userTypes = Usertype::lists('name', 'id');
        $userRole = $user->roles->lists('id', 'id')->toArray();
        $userDesignation = [
            $user->profile->designation => $user->profile->designation
        ];


        return view('users.edit', compact('user', 'roles', 'userRole', 'userTypes', 'userDesignation'));
    }

    /**
     * Show the resource details.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function getView($id)
    {
        $user = User::with('profile')->find($id);
        $userRole = $user->roles;
        $designation = $user->profile->designationName;
        $skills = Skillsused::select(DB::raw('skills.name as label'), DB::raw('skills.name as value'))
            ->leftJoin('skills', 'skills.id', '=', 'skills_used.skill_id')
            ->where('item_type', '=', 'user')
            ->where('item_id', '=', $id)
            ->get();

        return view('users.view', compact('user', 'userRole', 'designation', 'skills'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $messages = [
            'role.required' => 'Please select a role in the company.',
            'allocatable.required' => 'Please select allocatable or not.',
            'designation.required' => 'Please select a Designation.',
            'first_name.required' => 'Please add Firstname.',
            'last_name.required' => 'Please add Lastname.',
            'address_line1.required' => 'Please add Address Line 1.',
            'phone.required' => 'Please add Phone number.',
        ];

        $this->validate(
            $request, [
            'email' => 'required|email',
            'allocatable' => 'required',
            'role' => 'required',
            'designation' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'company' => 'required',
            'designation' => 'required',
            'address_line1' => 'required',
            'phone' => 'required',
        ], $messages
        );


        $input = $request->all();
        $input['first_name'] = trim($input['first_name']);
        $input['last_name'] = trim($input['last_name']);
        $input['email'] = trim($input['email']);

        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = array_except($input, array('password'));
        }


        $email = str_ireplace(' ', '', $input['email']);

        $role = $request->input('role');

        $user = User::find($id);


        /*check if mail already used starts*/
        $mailExists = DB::table('users')
            ->where('email', '=', $email)
            ->where('email', '!=', $user->email)
            ->count();

        if ($mailExists > 0) {
            return redirect()->route('resources.edit', $user->id)
                ->with('error', 'Email address already exists for another user');
        }
        /*check if mail already used ends*/


        /*check edit user downgrade starts*/

        $adminRoleId = DB::table('roles')->where('name', '=', 'admin')->value('id');

        if ($user->hasRole(['admin']) && $role != $adminRoleId) {

            $otherAdminCount = DB::table('role_user')
                ->where('role_id', '=', $adminRoleId)
                ->where('user_id', '<>', $user->id)
                ->count();

            if ($otherAdminCount == 0) {
                return redirect()->route('resources.edit', $user->id)
                    ->with('error', 'This user cannot be downgraded from super-admin access, no other admin exists');
            }

        }

        /*check edit user downgrade ends*/

        $user->update([
            'email' => $email,
            'allocatable' => $input['allocatable']
        ]);

        DB::table('role_user')
            ->where('user_id', $id)
            ->update(
                [
                    'role_id' => $role
                ]
            );
        $userProfile = Profile::where('user_id', $id)->first();
        if (array_key_exists("first_name", $input)) {
            $userProfile->first_name = $input['first_name'];
        }
        if (array_key_exists("last_name", $input)) {
            $userProfile->last_name = $input['last_name'];
        }
        if (array_key_exists("company", $input)) {
            $userProfile->company = $input['company'];
        }
        if (array_key_exists("designation", $input)) {
            $userProfile->designation = $input['designation'];
        }
        if (array_key_exists("address_line1", $input)) {
            $userProfile->address_line1 = $input['address_line1'];
        }
        if (array_key_exists("address_line2", $input)) {
            $userProfile->address_line2 = $input['address_line2'];
        }
        if (array_key_exists("phone", $input)) {
            $userProfile->phone = $input['phone'];
        }
        if (array_key_exists("city", $input)) {
            $userProfile->city = $input['city'];
        }
        if (array_key_exists("state", $input)) {
            $userProfile->state = $input['state'];
        }
        if (array_key_exists("country", $input)) {
            $userProfile->country = $input['country'];
        }
        if (array_key_exists("zipcode", $input)) {
            $userProfile->zipcode = $input['zipcode'];
        }

        $userProfile->update();

        //Manage skills

        $skillArray = explode(',', $input['user-skills']);

        $usedSkills = [];

        foreach ($skillArray as $tKey => $skill) {
            if (str_replace(" ", "", $skill) != "") {
                $skillExists = Skill::where('name', '=', trim($skill))->first();

                if (!is_null($skillExists)) {
                    //Check whether it already added
                    $addedSkill = Skillsused::where('skill_id', $skillExists->id)
                        ->where('item_type', 'user')
                        ->where('item_id', '=', $id)
                        ->first();
                    //If no add new skill to project
                    if (is_null($addedSkill)) {
                        Skillsused::create(['item_type' => 'user', 'skill_id' => $skillExists->id, 'item_id' => $id]);
                    }
                    $usedSkills[] = $skillExists->id;
                } else {
                    //Insert into skills table
                    $skillObj = Skill::create(['name' => trim($skill), 'author_id' => 2]);
                    //Insert into skills_used table
                    Skillsused::create(['item_type' => 'user', 'skill_id' => $skillObj->id, 'item_id' => $id]);
                    $usedSkills[] = $skillObj->id;
                }
            }
        }

        //Delete removed Skills
        Skillsused::whereNotIn('skill_id', $usedSkills)->where('item_type', 'user')->where(
            'item_id', '=',
            $id
        )->delete();

        return redirect()->route('resources.index')
            ->with('success', 'Resource updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function disable($id)
    {
        if (Auth::user()->id == $id) {
            return redirect()->route('resources.index')
                ->with('error', 'You cannot disable yourself');
        }

        $user = User::findOrFail($id);

        $adminRoleId = DB::table('roles')->where('name', '=', 'admin')->value('id');

        if ($user->hasRole(['admin'])) {

            $otherAdminCount = DB::table('role_user')
                ->where('role_id', '=', $adminRoleId)
                ->where('user_id', '<>', $user->id)
                ->count();

            if ($otherAdminCount == 0) {
                return redirect()->route('resources.index')
                    ->with('error', 'This user cannot be disabled');
            }

        }

        //check for future allocations
        $today = Carbon::now()->toDateString();
        //$allocations = Allocation::leftJoin('projects', 'allocations.project_id', '=', 'projects.id')
        /*->select(
            'allocations.id', DB::raw('allocations.project_id AS resourceId'),
            DB::raw('allocations.start_date AS start'),
            DB::raw('allocations.end_date AS end'),
            DB::raw('projects.name AS title'),
            'allocations.allocation_value'
        )*/
        $allocations = Allocation::where('allocations.assignee_id', '=', $user->id)
            ->whereRaw("DATE(allocations.end_date) > '" . $today . "'")
            ->count();
        if ($allocations > 0) {
            return redirect()->route('resources.index')
                ->with('error', 'This user have valid allocations, cannot be disabled');
        }


        $changeStatus = ($user->disabled == 0) ? 1 : 0;

        $user->update(
            [
                'disabled' => $changeStatus
            ]
        );

        return redirect()->route('resources.index')
            ->with('success', 'Resource ' . (($changeStatus == 1) ? "disabled" : "enabled") . ' successfully');
    }

    public function getAllocations(Request $request)
    {
        $input = $request->all();
        $allocations = Allocation::leftJoin('projects', 'allocations.project_id', '=', 'projects.id')
            ->select(
                'allocations.id', DB::raw('allocations.project_id AS resourceId'),
                DB::raw('allocations.start_date AS start'), DB::raw('allocations.end_date AS end'),
                DB::raw('projects.name AS title'), 'allocations.allocation_value'
            )
            ->whereRaw("allocations.assignee_id = " . $input['user_id'] . " AND ((DATE(allocations.start_date) >= '" . $input['start'] . "' AND DATE(allocations.start_date) < '" . $input['end'] . "') OR (DATE(allocations.end_date) > '" . $input['start'] . "' AND DATE(allocations.end_date) <= '" . $input['end'] . "') OR ('" . $input['start'] . "' > DATE(allocations.start_date) AND '" . $input['start'] . "' < DATE(allocations.end_date)) OR ('" . $input['end'] . "' > DATE(allocations.start_date) AND '" . $input['end'] . "' < DATE(allocations.end_date)))")
            ->get();
        $allocArray = $allocations->toArray();
        $allocArray = array_map(
            function ($alloc) {
                $alloc['title'] = $alloc['title'] . ' (' . $alloc['allocation_value'] . '%)';
                unset($alloc['allocation_value']);
                return $alloc;
            }, $allocArray
        );
        return \Response::json($allocArray, 200);
    }

    public function getProjects(Request $request)
    {
        $input = $request->all();
        $allocations = Projectroleuser::leftJoin('projects', 'projects_used.project_id', '=', 'projects.id')
            ->select('projects.id', DB::raw('projects.name AS title'))
            ->whereRaw('projects_used.user_id = ' . $input['user_id'])
            ->get();
        return \Response::json($allocations->toArray(), 200);
    }

    public function editBooking(Request $request, $id)
    {

        try {
            $allocation = Allocation::where('id', '=', $id)->first();

            $projectName = Project::where('id', '=', $allocation->project_id)->value('name');

            $note = Description::where(
                [
                    'item_id' => $id,
                    'item_type' => 'allocation'
                ]
            )->value('value');
            if (is_null($note)) {
                $note = '';
            }
            $allocationArray = $allocation->toArray();
            $allocationArray['note'] = $note;
            $allocationArray['project_name'] = $projectName;
            $allocationArray['start_date'] = date('Y-m-d', strtotime($allocationArray['start_date']));
            $allocationArray['end_date'] = date('Y-m-d', strtotime($allocationArray['end_date']));
            return \Response::json(['status' => 'success', 'data' => $allocationArray], 200);
        } catch (\Exception $e) {
            return \Response::json(['status' => 'error', 'message' => $e->getCode() . ' ' . $e->getMessage()], 200);
        }
    }

    public function deleteBooking(Request $request, $id)
    {

        try {
            Allocation::where('id', '=', $id)->delete();

            $note = Description::where(
                [
                    'item_id' => $id,
                    'item_type' => 'allocation'
                ]
            )->delete();

            return \Response::json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            return \Response::json(['status' => 'error', 'message' => $e->getCode() . ' ' . $e->getMessage()], 200);
        }
    }

    public function getSkills(Request $request, $id)
    {
        $skills = Skillsused::select(DB::raw('skills.name as label'), DB::raw('skills.name as value'))
            ->leftJoin('skills', 'skills.id', '=', 'skills_used.skill_id')
            ->where('item_type', '=', 'user')
            ->where('item_id', '=', $id)
            ->get();
        return \Response::json(['skills' => $skills], 200);
    }

    public function csvUpload()
    {

        return view('users.csv-upload');
    }

    public function csvUploadStore(Request $request)
    {

        if ($request->hasFile('file')) :
            $file = $request->file('file');

            $isValid = $this->isValidCSVFile($file);

            if (!$isValid) {
                return redirect()->route('resources.csv.upload')->with('error',
                    'Invalid file type, Please upload a csv file');
            }

            $csvData = array_map('str_getcsv', file($file->getPathName()));

            $errorBag = [];
            $errorCount = 0;
            $successCount = 0;

            foreach ($csvData as $key => $csv):
                if ($key == 0) {
                    continue;
                }
                $input = [];
                $input['first_name'] = trim($csv[0]);
                $input['last_name'] = trim($csv[1]);
                $input['email'] = trim($csv[2]);
                $input['user-skills'] = trim($csv[3]);
                $input['password'] = str_random(8);
                $plainPassword = $input['password'];
                $input['password'] = Hash::make($input['password']);

                /*validation starts*/
                $emailExists = DB::table('users')->where('email', '=', $input['email'])->count();

                if ($input['first_name'] == '' || $input['last_name'] == '' || $input['email'] == '') {
                    $errorCount++;
                    continue;
                } elseif ($emailExists > 0) {
                    array_push($errorBag, 'User with email:' . $input['email'] . ' already exists');
                    $errorCount++;
                    continue;
                }
                /*validation ends*/

                $input['role'] = 4;//developer
                $input['company'] = 'CubetTech';
                $input['user_type_id'] = 8;//software engineer
                $input['address_line1'] = 'CubetTech';
                $input['address_line2'] = 'Infopark';
                $input['phone'] = 'CubetTech';
                $input['city'] = 'Kochi';
                $input['state'] = 'Kerala';
                $input['country'] = 'India';
                $input['zipcode'] = '';


                DB::beginTransaction();
                try {
                    $user = User::create(
                        [
                            'email' => $input['email'],
                            'password' => $input['password']
                        ]
                    );

                    DB::table('role_user')
                        ->insert(
                            [
                                'user_id' => $user->id,
                                'role_id' => $input['role']
                            ]
                        );

                    $userProfile = Profile:: create(
                        [
                            'user_id' => $user->id,
                            'first_name' => $input['first_name'],
                            'last_name' => $input['last_name'],
                            'company' => $input['company'],
                            'designation' => $input['user_type_id'],
                            'address_line1' => $input['address_line1'],
                            'address_line2' => $input['address_line2'],
                            'phone' => $input['phone'],
                            'city' => $input['city'],
                            'state' => $input['state'],
                            'country' => $input['country'],
                            'zipcode' => $input['zipcode']
                        ]
                    );

                    //Manage Skills

                    $skillsArray = commaToArray($input['user-skills']);

                    foreach ($skillsArray as $sKey => $skill) {
                        $skill = trim($skill);

                        if ($skill == '') {
                            continue;
                        }

                        $skillExists = Skill::where('name', '=', $skill)->first();

                        if (count($skillExists) > 0) :
                            Skillsused::create(
                                [
                                    'item_type' => 'user',
                                    'skill_id' => $skillExists->id,
                                    'item_id' => $user->id
                                ]
                            );
                        else:
                            //Insert into skills table
                            $skillObj = Skill::create(['name' => $skill, 'author_id' => $user->id]);
                            //Insert into skills_used table
                            Skillsused::create(
                                [
                                    'item_type' => 'user',
                                    'skill_id' => $skillObj->id,
                                    'item_id' => $user->id
                                ]
                            );
                        endif;
                    }

                    /*QUEUE A EMAIL NOTIFICATION STARTS*/
                    $templateData = [
                        'firstName' => $userProfile->first_name,
                        'appName' => appName(),
                        'appUrl' => appUrl(),
                        'email' => $user->email,
                        'password' => $plainPassword
                    ];
                    $mailData = [
                        'to' => $user->email,
                        'name' => $userProfile->first_name,
                        'subject' => 'You have Been Invited To ' . appName()
                    ];

                    Mail::queue(
                        'emails.resource-invite', $templateData, function ($m) use ($mailData) {
                        $m->to($mailData['to'], $mailData['name'])
                            ->subject($mailData['subject']);
                    }
                    );
                    /*QUEUE A EMAIL NOTIFICATION ENDS*/

                    DB::commit();
                    $successCount++;
                } catch (\Exception $e) {
                    //rollback transaction if exception occurred while saving
                    DB::rollback();
                    array_push($errorBag,
                        'Error occurred while saving user with name:' . $input['first_name'] . 'and email:' . $input['email'] . $e->getMessage() . $e->getLine() . $e->getFile());
                    $errorCount++;
                }
            endforeach;


            if ($successCount > 0 && $errorCount > 0) :
                return redirect()->route('resources.csv.upload')
                    ->with('info',
                        $successCount . ' resources Successfully added and saving of ' . $errorCount . ' resources failed')
                    ->with('errorBag', $errorBag);
            elseif ($successCount > 0 && $errorCount == 0) :
                return redirect()->route('resources.csv.upload')
                    ->with('success', 'All ' . $successCount . ' resources Successfully added')
                    ->with('errorBag', $errorBag);
            elseif ($errorCount > 0 && $successCount == 0) :
                return redirect()->route('resources.csv.upload')
                    ->with('error', 'Saving of all ' . $errorCount . ' resources failed')
                    ->with('errorBag', $errorBag);
            else:
                return redirect()->route('resources.csv.upload')
                    ->with('errorBag', $errorBag);

            endif;

            return redirect()->route('resources.csv.upload')
                ->with('success', 'Successfully added resources');
        else:
            return redirect()->route('resources.csv.upload')
                ->with('error', 'Invalid file type, Please upload a csv file');
        endif;
        die;

    }

    public function isValidCSVFile($file)
    {
        //first we do simple extension checking
        $extension = strtolower($file->getClientOriginalExtension());


        $allowedExtensions = ['csv'];
        if (!in_array($extension, $allowedExtensions)) {
            return false;
        }

        //here we do not take client side mimetype, checks for actual mimetype.
        $finfo = new finfo(FILEINFO_MIME_TYPE);


        $mimeType = $finfo->file($file->getPathName());
        $allowedMimeType = [
            'text/csv',//.csv
            'text/plain',//.csv
        ];

        return in_array($mimeType, $allowedMimeType) ? true : false;
    }

    public function profile($id)
    {
        $user = User::with(['profile', 'allocations', 'assignedProjects'])->find($id);
        $userRole = $user->roles;
        $designation = $user->profile->designationName;
        $skills = Skillsused::select(DB::raw('skills.name as label'), DB::raw('skills.name as value'))
            ->leftJoin('skills', 'skills.id', '=', 'skills_used.skill_id')
            ->where('item_type', '=', 'user')
            ->where('item_id', '=', $id)
            ->get();
        return view('users.profile', compact('user', 'userRole', 'designation', 'skills'));

    }

    public function currentAllocoationData($id, Request $request)
    {
        $allocations = Allocation::where('assignee_id', $id);
        if (isset($request->start) && isset($request->end)) {
            $start = $request->start;
            $end = $request->end;
            $daysCount = Carbon::parse($request->end)->diffInDays(Carbon::parse($request->start));
            $startDate = Carbon::parse($request->start);
        } else {
            $startOfWeek = Carbon::now()->addDays(-6);
            $today = Carbon::now();
            $daysCount = $today->diffInDays($startOfWeek);
            $startDate = $startOfWeek;
            $start = $startOfWeek->toDateString();
            $end = $today->toDateString();
        }

        $allocations->whereRaw(" ((DATE(start_date) >= '" . $start .
            "' AND DATE(start_date) <= '" . $end . "') OR (DATE(end_date) >= '" . $start .
            "' AND DATE(end_date) <= '" . $end . "') OR ('" . $start . "' >= DATE(start_date) AND '" .
            $start . "' < DATE(end_date)) OR ('" . $end . "' > DATE(start_date) AND '" . $end .
            "' <= DATE(end_date)))");
        $allocations = $allocations->get();

        $data = [];
        $total = 0;
        $label = [];
        $keys = [];

        for ($day = 0; $day <= $daysCount; $day++) {
            $thisDay = Carbon::parse($startDate)->addDays($day);
            $dayObject = new \stdClass();
            foreach ($allocations as $key => $dayAllocation) {
                if (Carbon::parse($dayAllocation->start_date)->dayOfYear <= $thisDay->dayOfYear && Carbon::parse($dayAllocation->end_date)->dayOfYear >= $thisDay->dayOfYear) {
                    $Objkey = $key;
                    $dayObject->$Objkey = $dayAllocation->allocation_value;
                }

                if ($day == 0) {
                    $label[] = $dayAllocation->project->name;
                    $keys[] = $key;

                }
            }
            $dayObject->x = $thisDay->toFormattedDateString();
            $data[] = $dayObject;
        }

        return response()->json(
            [
                'status' => 'success',
                'data' => $data,
                'label' => $label,
                'keys' => $keys
            ]
        );
    }

    public function ajaxFilter(Request $request)
    {
        $username = $request->input('uname');
        $skills = $request->input('skills');
        $roles = $request->input('roles');
        $available = $request->input('available');
        $paginate = 20;

        $query = User::select(
            'users.*',
            'p.first_name',
            'p.last_name'
        )
            ->join('profiles as p', 'users.id', '=', 'p.user_id');

        if ($username != '') {
            $query->where(
                function ($qry) use ($username) {
                    $qry->where(DB::raw('CONCAT(p.first_name, " ", p.last_name)'), "LIKE", "%{$username}%")
                        ->orWhere("email", "LIKE", "%{$username}%");
                }
            );
        }

        if (count($skills)) {
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

        if (count($roles)) {
            $query->whereExists(
                function ($query) use ($roles) {
                    $query->select(DB::raw(1))
                        ->from('role_user')
                        ->whereRaw('role_user.user_id = users.id')
                        ->whereIn('role_id', $roles);
                }
            );
        }

        if ($available) {
            $query->whereNotExists(
                function ($query) {
                    $query->select(DB::raw(1))
                        ->from('allocations')
                        ->whereRaw('allocations.assignee_id = users.id')
                        ->where('start_date', '<=', Carbon::now())
                        ->where('end_date', '>=', Carbon::now());
                }
            );
        }

        $users = $query->orderBy('id', 'DESC')->paginate($paginate);

        $html = view('users.partials.user-search-result-block', ['users' => $users])->render();

        if ($request->ajax()) {
            return response()->json(
                [
                    'status' => 'success',
                    'html' => $html,
                ]
            );
        } else {
            $skills = Skill::all();
            $roles = Role::all();
            $search = $username;
            $inputSkills = $request->input('skills') ? $request->input('skills') : [];
            $inputRoles = $request->input('roles') ? $request->input('roles') : [];
            $isAvailable = $request->input('available') ? $request->input('available') : 0;
            return view('users.index',
                compact('users', 'skills', 'roles', 'search', 'inputSkills', 'inputRoles', 'isAvailable'))
                ->with('i', ($request->input('page', 1) - 1) * $paginate);
        }


    }
}
