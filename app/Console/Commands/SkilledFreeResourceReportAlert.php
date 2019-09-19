<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Skill;
use App\User;
use DB,Mail;

class SkilledFreeResourceReportAlert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:SkilledFreeResourceReportAlert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Skilled Free Resource Report Alert';

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
        $today = Carbon::now()->toDateString();

        $skills = Skill::select(DB::raw('skills.*, (SELECT COUNT(su.id) FROM skills_used su WHERE su.skill_id = skills.id) AS usedCount'))
            ->whereExists(function($query){
                $query->select(DB::raw(1))
                    ->from('skills_used')
                    ->whereRaw('skills_used.skill_id = skills.id')
                    ->where('skills_used.item_type','=','user');
            })
            ->orderBy('usedCount','DESC')
            ->get();

        if(count($skills)==0){return false;}

        $templateData = [
            'skills' => $skills
        ];

        /*$template = view('emails.skilled-free-resource-report-alert',$templateData)->render();
        file_put_contents(public_path() . '/mails/test-skill.html', $template);*/


        $permissionId = DB::table('permissions')
            ->where('name','=','skill-resource-report-mail')
            ->value('id');

        if($permissionId!=''):
            $roleIds = DB::table('permission_role')
                ->where('permission_id','=',$permissionId)
                ->lists('role_id');

            $mailableUsers = User::select(
                'users.id',
                'users.email',
                'profiles.first_name'
            )
                ->join('profiles','user_id','=','users.id')
                ->whereExists(function($query)use($roleIds){
                    $query->select(DB::raw(1))
                        ->from('role_user')
                        ->whereRaw('role_user.user_id = users.id')
                        ->whereIn('role_user.role_id',$roleIds);
                })
                ->get();

            foreach($mailableUsers as $user){
                $templateData['firstName']=$user->first_name;

                $mailData = [
                    'to' => $user->email,
                    'name' => $user->first_name,
                    'subject' => 'Skilled Free Resource Report'
                ];
                Mail::send('emails.skilled-free-resource-report-alert', $templateData, function ($m) use ($mailData) {
                    $m->to($mailData['to'], $mailData['name'])
                        ->subject($mailData['subject']);
                });
            }
        endif;


    }
}
