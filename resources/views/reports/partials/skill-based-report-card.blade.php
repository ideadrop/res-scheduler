@foreach($skills as $skill)
    <?php
    $skillsUsed = $skill->skillsUsed;

    $usedProjects = $skill->usedProjects;
    $usedProjectsCount = count($usedProjects);

    $usedResources = $skill->usedResources;
    // foreach($usedResources as $res){
    //       dd($res->resource);
    // }


    $usedResourcesCount = count($usedResources);
    ?>
    <div class="project-report-card">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">{{$skill->name}}</h3>
                <div class="pull-right"></div>
            </div>
            <div class="panel-body">
                @if($usedProjectsCount==0 && $usedResourcesCount==0)
                    <p>No projects/resources found with this skill</p>
                @endif

                @if($usedResourcesCount>0)
                <h3>Resources</h3>
                <table class="table">
                    <thead>
                      <tr>
                          <th>Name</th>
                          <th>Skills</th>
                          <th>Roles</th>
                          <th width="300px">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                    @foreach($usedResources as $usedResource)
                    <?php
                    $resource = $usedResource->resource;
                    $resourceProfile = $resource->profile;
                    ?>
                      @include( 'users.partials.resource-table-row', array( 'user' => $resource ) )
                    @endforeach
                    </tbody>
                </table>
                @endif

                @if($usedProjectsCount>0)
                <h3>Projects</h3>
                <table class="table">
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
                    @foreach($usedProjects as $usedProject)
                        <?php $project = $usedProject->project; ?>

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
