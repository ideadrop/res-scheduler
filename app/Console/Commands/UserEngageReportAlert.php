<?php

namespace App\Console\Commands;

use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use DB,Mail;


class UserEngageReportAlert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:UserEngageReportAlert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'User engage mail report';

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

        $engagedUsers = User::whereExists(function($query) use($today){
                            $query->select(DB::raw(1))
                                ->from('allocations')
                                ->whereRaw('allocations.assignee_id = users.id')
                                ->whereRaw("DATE(allocations.end_date) >= '$today'");
                        })
                        ->get();
        /*$freeUsers = User::whereNotExists(function($query) use($today){
                        $query->select(DB::raw(1))
                            ->from('allocations')
                            ->whereRaw('allocations.assignee_id = users.id')
                            ->whereRaw("DATE(allocations.end_date) >= '$today'");
                    })
                    ->get();*/

        $freeUsers = User::select(
                                'users.*',
                                DB::raw('CONCAT(profiles.first_name, " ", profiles.last_name) AS full_name')
                            )
                            ->join('profiles', 'users.id', '=', 'profiles.user_id')
                            ->whereNotExists(function($query) use($today){
                                $query->select(DB::raw(1))
                                    ->from('allocations')
                                    ->whereRaw('allocations.assignee_id = users.id')
                                    ->whereRaw("DATE(allocations.start_date) <= '$today'")
                                    ->whereRaw("DATE(allocations.end_date) > '$today'");
                            })
                            ->get();
        $engagedUsers = User::select(
            'users.*',
            DB::raw('CONCAT(profiles.first_name, " ", profiles.last_name) AS full_name')
        )
            ->join('profiles', 'users.id', '=', 'profiles.user_id')
            ->whereExists(function($query) use($today){
                $query->select(DB::raw(1))
                    ->from('allocations')
                    ->whereRaw('allocations.assignee_id = users.id')
                    ->whereRaw("DATE(allocations.start_date) <= '$today'")
                    ->whereRaw("DATE(allocations.end_date) > '$today'");
            })
            ->get();

        if(count($engagedUsers)==0 && count($freeUsers)==0){return false;}

        $templateData = [
            'engagedUsers' => $engagedUsers,
            'freeUsers' => $freeUsers
        ];

        /*$template = view('emails.resource-engage-report-alert',$templateData)->render();
        file_put_contents(public_path() . '/mails/test-engage.html', $template);*/

        $permissionId = DB::table('permissions')
            ->where('name','=','resource-engage-report-mail')
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
                    'subject' => 'Resource Engage Report'
                ];
                Mail::send('emails.resource-engage-report-alert', $templateData, function ($m) use ($mailData) {
                    $m->to($mailData['to'], $mailData['name'])
                        ->subject($mailData['subject']);
                });
            }
        endif;



    }
}
