<?php

namespace App\Jobs;

use App\Allocation;
use Mail;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProjectAllocationMail extends Job implements ShouldQueue
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


        $allocationId = $this->data['allocation_id'];


        $allocation = Allocation::find($allocationId);

        $templateData = [
            'firstName' => $allocation->assigneeProfile->first_name,
            'projectName' => $allocation->project->name,
            'assignerName' =>  $allocation->assignerProfile->first_name,
            'allocation' =>  $allocation->allocation_value,
            'description' =>  $allocation->present()->description,
            'startDate' => formatAllocationDate($allocation->start_date),
            'endDate' => formatAllocationDate($allocation->end_date)
        ];

        /*$template = view('emails.resource-project-added-allocation',$templateData)->render();
        file_put_contents(public_path() . '/mails/test-created.html', $template);*/

        $mailData = [
            'to' => $allocation->assignee->email,
            'name' => $templateData['firstName'],
            'subject' => $templateData['projectName'].': New task assigned'
        ];
        Mail::send('emails.resource-project-added-allocation', $templateData, function ($m) use ($mailData) {
            $m->to($mailData['to'], $mailData['name'])
                ->subject($mailData['subject']);
        });


    }
}
