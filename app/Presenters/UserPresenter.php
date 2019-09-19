<?php namespace App\Presenters;

use Laracasts\Presenter\Presenter;
use DB;
use App\Allocation;

use Carbon\Carbon;
class UserPresenter extends Presenter
{
    public function type(){
        return 'user';
    }
    public function fullName()
    {
        $profile = $this->profile;
        return $profile->first_name.' '.$profile->lst_name;
    }

    public function designation()
    {
        $designationId = $this->profile->designation;
        return DB::table('user_types')->where('id','=',$designationId)->value('name');
    }

    public function projectCount()
    {
        $userId = $this->id;
        return DB::table('projects_used')->where('user_id','=',$userId)->count();
    }

    public function upcomingAllocation(){

        $today = Carbon::now()->toDateString();

        return Allocation::where('assignee_id', '=', $this->id)
            ->whereRaw("DATE(start_date) > '$today'")
            ->orderBy('start_date')
            ->first();

    }
    public function todayAllocations(){
        $start = Carbon::now()->toDateString();
        $end = Carbon::now()->addDay()->toDateString();
        $userId = $this->id;
        $inputs = [
            'start_date'=>$start,
            'end_date'=>$end
        ];
        $todayAllocations = DB::table('allocations as al')
                ->select(
                    'al.allocation_value as percentage',
                    'projects.name'
                )
                ->join('projects','al.project_id','=','projects.id')
                ->where('al.assignee_id', '=', $userId)
                ->whereRaw(" ((DATE(al.start_date) >= '" . $inputs['start_date'] . "' AND DATE(al.start_date) < '" . $inputs['end_date'] . "') OR (DATE(al.end_date) > '" . $inputs['start_date'] . "' AND DATE(al.end_date) <= '" . $inputs['end_date'] . "') OR ('" . $inputs['start_date'] . "' > DATE(al.start_date) AND '" . $inputs['start_date'] . "' < DATE(al.end_date)) OR ('" . $inputs['end_date'] . "' > DATE(al.start_date) AND '" . $inputs['end_date'] . "' < DATE(al.end_date)))")
                //->whereRaw("DATE(allocations.start_date) <= '$today'")
                //->whereRaw("DATE(allocations.end_date) > '$today'");
            ->get();
        return $todayAllocations;
    }
    public function upcomingAllocationText(){

        $allocation = $this->upcomingAllocation();

        if(count($allocation)>0) {

            return sprintf(
                "%s : From %s to %s",
                $allocation->project->name,
                Carbon::parse($allocation->start_date)->toFormattedDateString(),
                Carbon::parse($allocation->end_date)->toFormattedDateString()

            );
        }else{
            return 'No Future Allocations';
        }

    }
    public function getRoleId(){

        return DB::table('role_user')->where('user_id','=',$this->id)->value('role_id');
    }

}
