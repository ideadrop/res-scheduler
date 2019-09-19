<?php namespace App\Presenters;

use Laracasts\Presenter\Presenter;
use DB;
class ProjectPresenter extends Presenter
{

    public function type(){
        return 'project';
    }
    public function description()
    {
        return DB::table('descriptions')
            ->where('item_type','=','project')
            ->where('item_id','=',$this->entity->id)
            ->value('value');
    }
    public function description1()
    {
        return DB::table('descriptions')
            ->where('item_type','=','project')
            ->where('item_id','=',$this->entity->id)
            ->value('value');
    }


}
