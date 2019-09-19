<tr>
    <td><a href='{{route('resources.profile', ["id"=>$user->id])}}'>{{ $user->profile->first_name }} {{ $user->profile->last_name }}</a></td>
    <td>
        <?php $todayAllocations = $user->present()->todayAllocations(); ?>
        @if(count($todayAllocations)==0)
        -------
        @endif
        @foreach($user->present()->todayAllocations() as $allocation)
            <label class="label label-info">{{$allocation->name}} : {{$allocation->percentage}}%</label><br>
        @endforeach
    </td>
    <td>
        @if(count($user->skills)==0)
        -------
        @endif
        @if(!empty($user->skills))
        @foreach($user->skills as $skill)
        <label class="label label-success">{{ $skill->skill->name }}</label>
        @endforeach
        @endif
    </td>
    <td>
        @if(!empty($user->roles))
        @foreach($user->roles as $v)
        <label class="label label-success">{{ $v->display_name }}</label>
        @endforeach
        @endif
    </td>
    <td>
        @permission('resource-list-allocate')
        <a class="btn btn-primary" href="{{ route('resources.view',$user->id) }}">View</a>
        @endpermission
        <a class="btn btn-info" href="{{ route('resources.show',$user->id) }}">Timeline</a>
        @permission('resource-edit')
        <a class="btn btn-primary" href="{{ route('resources.edit',$user->id) }}">Edit</a>
        @endpermission
        @permission('resource-disable-enable')
            {!! Form::open(['method' => 'POST','route' => ['resources.disable', $user->id],'style'=>'display:inline']) !!}
            @if($authUser->id != $user->id)
            <?php $btnClass = ($user->disabled==0)?'btn-danger':'btn-success';?>
            {!! Form::submit(($user->disabled==0)?'Disable':'Enable', ["class" => "btn $btnClass"]) !!}
            @else
                {!! Form::button('Disable', ['class' => 'btn btn-default','disabled'=>'disabled']) !!}
            @endif
            {!! Form::close() !!}
        @endpermission
    </td>
</tr>
