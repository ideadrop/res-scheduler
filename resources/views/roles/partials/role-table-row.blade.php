<tr>
    <td>{{ $role->display_name }}</td>
    <td>{{ $role->description }}</td>
    <td>
        @permission('role-list')
        <a class="btn btn-info" href="{{ route('roles.show',$role->id) }}">Show</a>
        @endpermission
        @permission('role-edit')
        <a class="btn btn-primary" href="{{ route('roles.edit',$role->id) }}">Edit</a>
        @endpermission
        @if($role->name!='admin')
            @permission('role-delete')
            {!! Form::open(['class'=>'role-delete-form','method' => 'DELETE','route' => ['roles.destroy', $role->id],'style'=>'display:inline']) !!}
            {!! Form::button('Delete', ['class' => 'btn btn-danger role-delete-btn']) !!}
            {!! Form::close() !!}
            @endpermission
        @else
            {!! Form::button('Delete', ['class' => 'btn btn-default','disabled'=>'disabled']) !!}
        @endif
    </td>
</tr>
