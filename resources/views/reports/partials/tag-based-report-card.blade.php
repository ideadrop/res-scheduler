@foreach($tags as $tag)
    <?php
    $taggedProjects = $tag->getTaggedProjects;
    $taggedProjectsCount = count($taggedProjects);

    $taggedResources = $tag->getTaggedResources;
    $taggedResourcesCount = count($taggedResources);
    ?>
    <div class="project-report-card">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">{{$tag->name}}</h3>
                <div class="pull-right"></div>
            </div>
            <div class="panel-body">
                @if($taggedProjectsCount==0 && $taggedResourcesCount==0)
                    <p>No projects/resources found with this skill</p>
                @endif

                @if($taggedResourcesCount>0)
                    <h2>Resources</h2>
                    @foreach($taggedResources as $taggedResource)
                    <?php
                    $resource = $taggedResource->resource;
                    $resourceProfile = $resource->profile;
                    ?>
                    <div class="project-report-user-card">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">{{$resourceProfile->first_name.' '.$resourceProfile->last_name}}</h3>
                                <div class="pull-right"></div>
                            </div>
                            <div class="panel-body">
                                <div class="report-project-details-container" style="font-weight: 700;">
                                    <div class="row">
                                        <div class="col-md-2">FIRST NAME</div>
                                        <div class="col-md-10">: {{$resourceProfile->first_name}}</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">LAST NAME</div>
                                        <div class="col-md-10">: {{$resourceProfile->last_name}}</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">EMAIL</div>
                                        <div class="col-md-10">: {{$resource->email}}</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">DESIGNATION</div>
                                        <div class="col-md-10">: {{$resource->present()->designation}}</div>
                                    </div>
                                </div>
                                <hr>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif

                @if($taggedProjectsCount>0)
                <h2>Projects</h2>
                <table class="table table-hover">
                    <thead>
                      <tr>
                          <th>Project Name</th>
                          <th>Project Code</th>
                          <th>Skills</th>
                          <th>Status</th>
                          <th width="300px">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach($taggedProjects as $taggedProject)
                        <?php $project = $taggedProject->project; ?>

                        @if(count($project)>0)
                            @include( 'projects.partials.project-table-row', array( 'project' => $project ) )
                        @endif
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>
@endforeach
