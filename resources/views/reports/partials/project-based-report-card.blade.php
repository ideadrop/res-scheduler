@foreach($projects as $project)
    <div class="project-report-card">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">{{$project->name}}</h3>
                <div class="pull-right"></div>
            </div>
            <div class="panel-body">
                <div class="report-project-details-container" style="font-weight: 700;">
                    <div class="row">
                        <div class="col-md-2">PROJECT NAME</div>
                        <div class="col-md-10">: {{$project->name}}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">PROJECT CODE</div>
                        <div class="col-md-10">: {{$project->project_code}}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">DESCRIPTION</div>
                        <div class="col-md-10">: {{$project->present()->description()}}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">START DATE</div>
                        <div class="col-md-10">: {{formatAllocationDate($project->start_date)}}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">END DATE</div>
                        <div class="col-md-10">: {{formatAllocationDate($project->end_date)}}</div>
                    </div>
                </div>
                <hr>
                <h2>Resources</h2>
                @foreach($project->resources as $resource)
                <?php
                    $resourceUser = $resource->user;
                    $resourceProfile = $resourceUser->profile;
                    $resourceAllocations = $resourceUser->allocations($project->id)->get();
                ?>
                <div class="project-report-user-card">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">{{$resourceProfile->first_name.' '.$resourceProfile->last_name}}</h3>
                            <div class="pull-right"></div>
                        </div>
                        <div class="panel-body">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Allocation %</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(count($resourceAllocations)==0)
                                    <tr><td colspan="3">{{ucfirst($resourceProfile->first_name)}} is a member of this project but no allocations were found</td></tr>
                                @endif
                                @foreach($resourceAllocations as $allocation)
                                <tr>
                                    <td>{{formatAllocationDate($allocation->start_date)}}</td>
                                    <td>{{formatAllocationDate($allocation->end_date)}}</td>
                                    <td>{{$allocation->allocation_value}}</td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
@endforeach