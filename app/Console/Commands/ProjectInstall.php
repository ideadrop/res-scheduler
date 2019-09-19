<?php

namespace App\Console\Commands;

use App\Roleuser;
use Illuminate\Console\Command;

use App\User;
use App\Profile;
use DB;
class ProjectInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Application';

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
            $data =[];
            $data['first_name'] = $this->anticipate('Enter Super admin first name',['Super','Admin']);
            $data['last_name'] = $this->anticipate('Enter super admin last name',['Admin','Administrator']);
            $data['email'] = $this->ask('Enter super admin email id');
            $data['password'] = $this->secret('Enter super admin password');
            $data['company_name'] = $this->anticipate('Enter company name',['Cubet','CubetTech','Cubet Tech']);

            $designations = DB::table('user_types')->select('id','name')->get();
            $designationsRows = [];
            foreach($designations as $designation){
                $designationsRows[]=[$designation->id,$designation->name];
            }
            $this->table(['ID','Name'], $designationsRows);
            $data['designation'] = $this->ask('Enter Super admin designation number from above list');

            $designationExists = DB::table('user_types')->where('id','=',$data['designation'])->count();
            if($designationExists==0){
                $this->error("\nInvalid Designation Number");
                die;
            }

            $user = new User();
            $user->email = $data['email'];
            $user->password = bcrypt($data['password']);
            $user->save();

            $profile = new Profile();
            $profile->user_id = $user->id;
            $profile->first_name = $data['first_name'];
            $profile->last_name = $data['last_name'];
            $profile->company = $data['company_name'];
            $profile->designation = $data['designation'];
            $profile->save();

            $role = new Roleuser();
            $role->user_id = $user->id;
            $role->role_id = 1;
            $role->save();

            //commit transaction
            DB::commit();

            $this->info("\nSuper admin successfully created and now you can login to application");

        } catch (\Exception $e) {
            //rollback transaction if exception occurred
            DB::rollback();
            throw $e;
        }


    }
}
