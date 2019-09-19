<?php

namespace App\Jobs;

use App\Allocation;
use Mail;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeleteProjectAllocationMail extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        if ($this->attempts() > 2) {
            $this->delete();
        }


        $data = $this->data;


        $templateData = [
            'firstName' => $data['assignee_name'],
            'projectName' => $data['project_name'],
            'assignerName' =>  $data['assigner_name'],
            'allocation' =>  $data['allocation'],
            'startDate' => formatAllocationDate($data['start_date']),
            'endDate' => formatAllocationDate($data['end_date'])
        ];

        /*$template = view('emails.resource-project-removed-allocation',$templateData)->render();
        file_put_contents(public_path() . '/mails/test-deleted.html', $template);*/


        $mailData = [
            'to' => $data['assignee_mail'],
            'name' => $templateData['firstName'],
            'subject' => $templateData['projectName'].': Allocation Removed'
        ];
        Mail::send('emails.resource-project-removed-allocation', $templateData, function ($m) use ($mailData) {
            $m->to($mailData['to'], $mailData['name'])
                ->subject($mailData['subject']);
        });


    }
}
