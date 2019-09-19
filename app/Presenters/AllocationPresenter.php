<?php namespace App\Presenters;

use Laracasts\Presenter\Presenter;
use DB;

class AllocationPresenter extends Presenter
{

    public function test()
    {
        print_r('rameez');die;
    }

    public function description(){
        $content = DB::table('descriptions')
            ->where('item_type','=','allocation')
            ->where('item_id','=',$this->id)
            ->value('value');


        return strip_tags($content);
    }

}
