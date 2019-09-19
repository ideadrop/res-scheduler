<?php

namespace App\Console\Commands;

use App\Permission;
use App\Role;
use DB;
use Illuminate\Console\Command;

class AllocatableMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:allocatableMigration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Allocatable Migration';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::beginTransaction();
        try {

            ##### DELETE PM allocations #####
            $pmRoleId = DB::table('roles')->where('name','=','project_manager')->value('id');
            $pmUserIds = DB::table('role_user')->where('role_id','=',$pmRoleId)->lists('user_id');
            $allocationIds = DB::table('allocations')->whereIn('assignee_id',$pmUserIds)->lists('id');
            DB::table('descriptions')
                ->where('item_type','=','allocation')
                ->whereIn('item_id',$allocationIds)
                ->delete();
            DB::table('allocations')->whereIn('id',$allocationIds)->delete();

            //commit transaction
            DB::commit();

            $this->info("\nAllocatable Permission Migration completed");

        } catch (\Exception $e) {
            //rollback transaction if exception occurred
            DB::rollback();
            throw $e;
        }


    }
}
