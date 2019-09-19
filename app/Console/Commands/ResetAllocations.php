<?php

namespace App\Console\Commands;

use App\Allocation;
use App\Description;
use App\Roleuser;
use Illuminate\Console\Command;

use App\User;
use App\Profile;
use DB;
class ResetAllocations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-allocation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset Project Allocations';

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
        try{

            DB::table('allocations')->delete();

            DB::table('descriptions')->where('item_type','=','allocation')->delete();

            $this->info("All allocations cleared");

        } catch (\Exception $e) {
            //rollback transaction if exception occurred
            DB::rollback();
            throw $e;
        }


    }
}
