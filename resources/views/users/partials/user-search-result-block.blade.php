<table class="table table-hover">
    <tr>
        <th>Name</th>
        <th>Today's Allocations</th>
        <th>Skills</th>
        <th>Roles</th>
        <th width="300px">Action</th>
    </tr>
    @if(count($users)==0)
        <tr>
            <th colspan="5">No Resource found</th>
        </tr>
    @endif
    @foreach ($users as $key => $user)
      @include( 'users.partials.resource-table-row', array( 'user' => $user ) )
    @endforeach
</table>
<div class="ajaxpagination">{{$users->appends($_GET)->links()}}</div>
