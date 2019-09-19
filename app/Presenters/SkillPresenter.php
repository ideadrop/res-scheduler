<?php namespace App\Presenters;

use Laracasts\Presenter\Presenter;
use DB;
use Carbon\Carbon;

class SkillPresenter extends Presenter
{

    public function freeSkilledUserCount(){

        $today = Carbon::now()->toDateString();

        $skillId = $this->id;

        $resourcesCount = DB::table('users')
                            ->whereExists(function($query) use($skillId){
                                $query->select(DB::raw(1))
                                    ->from('skills_used')
                                    ->whereRaw('skills_used.item_id = users.id')
                                    ->where('skills_used.skill_id','=',$skillId)
                                    ->where('skills_used.item_type','=','user');
                            })
                            ->whereNotExists(function($query) use($today){
                                $query->select(DB::raw(1))
                                    ->from('allocations')
                                    ->whereRaw('allocations.assignee_id = users.id')
                                    ->whereRaw("DATE(allocations.start_date) <= '$today'")
                                    ->whereRaw("DATE(allocations.end_date) > '$today'");
                            })
                            ->count();


        return $resourcesCount;



    }


}
