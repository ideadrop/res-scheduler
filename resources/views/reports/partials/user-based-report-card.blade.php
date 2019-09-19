@foreach($resourceUsers as $resourceUser)
    <?php
    $assignedProjects = $resourceUser->assignedProjects;
    $allocations = $resourceUser->allocations;
    ?>
    <div class="project-report-card">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">{{$resourceUser->first_name.' '.$resourceUser->last_name}}</h3>
                <div class="pull-right"></div>
            </div>
            <div class="panel-body">
                @if(count($allocations)==0)
                    <p>No allocations found for this user</p>
                @else
                <div class="project-report-user-card">
                  <table class="table">
                  <thead>
                    <tr>
                      <th>Project</th>
                      <th>Date</th>
                      <th>Allocation</th>
                    </tr>
                  </thead>
                  <tbody>

                  @foreach($allocations as $allocation)
                    <tr>
                      <td>{{$allocation->project->name}}</td>
                      <td>{{ \Carbon\Carbon::parse($allocation->start_date)->toFormattedDateString()}} to {{ \Carbon\Carbon::parse($allocation->end_date)->toFormattedDateString()}}</td>
                      <td>{{$allocation->allocation_value}}%</td>
                    </tr>
                  @endforeach
                  </tbody>
                </table>
                </div>
              @endif
            </div>
        </div>
    </div>
@endforeach
