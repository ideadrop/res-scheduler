<?php

namespace App\Console\Commands;

use App\Allocation;
use App\User;
use Illuminate\Console\Command;
use Carbon\Carbon;
use DB,Mail;


class ResourceAllocationEndingAlert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:ResourceAllocationEndingAlert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resource Allocation Ending Alert';

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
        $days = 7;
        $checkDate = Carbon::now()->addDays($days)->toDateString();

        $allocations = DB::table('allocations')
            ->select('id','project_id','assignee_id')
            ->whereRaw("DATE(end_date) = '$checkDate'")
            ->get();
        $endingAllocationIds =[];
        foreach($allocations as $allocation):
            $futureAllocations = DB::table('allocations')
                ->whereRaw("DATE(end_date) > '$checkDate'")
                ->where('project_id','=',$allocation->project_id)
                ->where('assignee_id','=',$allocation->assignee_id)
                ->count();
            if($futureAllocations==0){
                array_push($endingAllocationIds,$allocation->id);
            }
        endforeach;

        $allocations = Allocation::whereIn('id',$endingAllocationIds)
            ->groupBy('project_id')
            ->get();

        if(count($allocations)==0){return false;}
        $templateData = [
            'allocations' => $allocations,
            'date' => Carbon::parse($checkDate)->toFormattedDateString()
        ];

        /*$template = view('emails.resource-allocation-ending-alert',$templateData)->render();
        file_put_contents(public_path() . '/mails/test-ending.html', $template);*/



        $permissionId = DB::table('permissions')
            ->where('name','=','resource-release-mail')
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
                    'subject' => 'Resource going to be released soon'
                ];
                Mail::send('emails.resource-allocation-ending-alert', $templateData, function ($m) use ($mailData) {
                    $m->to($mailData['to'], $mailData['name'])
                        ->subject($mailData['subject']);
                });
            }
        endif;





    }
}
