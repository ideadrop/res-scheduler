<table class="table table-hover">
    <tr>
        <th>Name</th>
        <th>Description</th>
        <th width="280px">Action</th>
    </tr>
    @if(count($roles)==0)
        <tr>
            <th colspan="5">No Roles found</th>
        </tr>
    @endif
    @foreach ($roles as $key => $role)
        @include( 'roles.partials.role-table-row', array( 'role' => $role ) )
    @endforeach
</table>
<div class="ajaxpagination">{!! $roles->render() !!}</div>
