<?php namespace App\Http\Controllers;

use App\Skill;
use App\User;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function home()
    {
        if (Auth::check()):
            $authUser = Auth::user();
            if ($authUser->can('view-dashboard')) {
                return redirect()->route('dashboard');
            } else {
                if ($authUser->can('view-my-project-calender')) {
                    return redirect()->route('resources.show', $authUser->id);
                } else {
                    return view('welcome');
                }
            }
        else:
            return redirect()->route('auth.login');
            return view('welcome');
        endif;
    }

    public function dashboard()
    {
        return view('dashboard.index');
    }

    public function getDashboardSkillDonutData(Request $request)
    {

        $inputs = $request->all();
        $type = $inputs['type'];

        $skills = Skill::select(DB::raw('skills.name as label, (SELECT COUNT(su.id) FROM skills_used su WHERE su.skill_id = skills.id AND su.item_type="' . $type . '") AS value'))
            ->whereRaw('(SELECT COUNT(su.id) FROM skills_used su WHERE su.skill_id = skills.id AND su.item_type="' . $type . '") <> 0')
            ->orderBy('value', 'DESC')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $skills
        ]);
    }

    public function getDashboardFreeResources()
    {
        $today = Carbon::now()->toDateString();

        /*$resources = DB::table('users')
                    ->select(
                        'users.id',
                        'users.email',
                        DB::raw('CONCAT(profiles.first_name, " ", profiles.last_name) AS full_name')
                    )
                    ->join('profiles', 'users.id', '=', 'profiles.user_id')
                    ->whereNotExists(function($query) use($today){
                        $query->select(DB::raw(1))
                            ->from('allocations')
                            ->whereRaw('allocations.assignee_id = users.id')
                            ->whereRaw("DATE(allocations.end_date) >= '$today'");
                    })
                    ->get();*/


        $resources = User::select(
                                'users.id',
                                'users.email',
                                DB::raw('CONCAT(profiles.first_name, " ", profiles.last_name) AS full_name')
                            )
                            ->enabled()->allocatable()
                            ->join('profiles', 'users.id', '=', 'profiles.user_id')
                            ->whereNotExists(function ($query) use ($today) {
                                $query->select(DB::raw(1))
                                    ->from('allocations')
                                    ->whereRaw('allocations.assignee_id = users.id')
                                    ->whereRaw("DATE(allocations.start_date) <= '$today'")
                                    ->whereRaw("DATE(allocations.end_date) > '$today'");
                            })
                            ->get();


        $html = view('dashboard.partials.dashboard-free-user-cards', ['resources' => $resources])->render();

        return response()->json([
            'status' => 'success',
            'html' => $html
        ]);
    }

    public function getDashboardActiveProjects()
    {
        $inputs = [
            'start_date' => Carbon::now()->toDateString(),
            'end_date' => Carbon::tomorrow()->toDateString()
        ];
        $projects = DB::table('projects')
            ->select('id', 'name', 'project_code')
            ->whereExists(function ($query) use ($inputs) {
                $query->select(DB::raw(1))
                    ->from('allocations as al')
                    ->whereRaw('al.project_id = projects.id')
                    ->whereRaw(" ((DATE(al.start_date) >= '" . $inputs['start_date'] . "' AND DATE(al.start_date) < '" . $inputs['end_date'] . "') OR (DATE(al.end_date) > '" . $inputs['start_date'] . "' AND DATE(al.end_date) <= '" . $inputs['end_date'] . "') OR ('" . $inputs['start_date'] . "' > DATE(al.start_date) AND '" . $inputs['start_date'] . "' < DATE(al.end_date)) OR ('" . $inputs['end_date'] . "' > DATE(al.start_date) AND '" . $inputs['end_date'] . "' < DATE(al.end_date)))");
            })
            ->get();

        $html = view('dashboard.partials.dashboard-active-project-cards', ['projects' => $projects])->render();

        return response()->json([
            'status' => 'success',
            'html' => $html
        ]);
    }


}
