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
    @if(count($projects)==0)
        <tr>
            <th colspan="5">No projects found</th>
        </tr>
    @endif
    @foreach($projects as $key => $project)
        @include( 'projects.partials.project-table-row', array( 'project' => $project ) )
    @endforeach
    </tbody>
</table>
<div class="ajaxpagination">{{$projects->appends($_GET)->links()}}</div>
