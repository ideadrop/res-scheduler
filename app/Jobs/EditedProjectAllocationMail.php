<?php

namespace App\Jobs;

use App\Allocation;
use Mail;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EditedProjectAllocationMail extends Job implements ShouldQueue
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
        $changeCheckValues = $this->data['changeCheckValues'];


        $allocation = Allocation::find($allocationId);

        $templateData = [
            'changeCheckValues' => $changeCheckValues,
            'firstName' => $allocation->assigneeProfile->first_name,
            'projectName' => $allocation->project->name,
            'assignerName' =>  $allocation->assignerProfile->first_name,
            'allocation' =>  $allocation->allocation_value,
            'allocationDescription' =>  $allocation->present()->description(),
            'startDate' => formatAllocationDate($allocation->start_date),
            'endDate' => formatAllocationDate($allocation->end_date)
        ];

        /*$template = view('emails.resource-project-edited-allocation',$templateData)->render();
        file_put_contents(public_path() . '/mails/test-edited.html', $template);*/

        $mailData = [
            'to' => $allocation->assignee->email,
            'name' => $templateData['firstName'],
            'subject' => $templateData['projectName'].': Allocation Edited'
        ];
        Mail::send('emails.resource-project-edited-allocation', $templateData, function ($m) use ($mailData) {
            $m->to($mailData['to'], $mailData['name'])
                ->subject($mailData['subject']);
        });


    }
}
