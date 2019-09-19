@if(count($resources)==0)
    <tr>
        <td colspan="4">
            <div class="well text-center"> No Free Resources</div>
        </td>
    </tr>
@else:
    @foreach($resources as $index=>$resource)
        <tr>
            <td>{{$index+1}}</td>
            @if($authUser->can('resource-list-allocate'))
                <td>
                    <a href="{{ route('resources.profile',$resource->id) }}" title="View Resource Calender">{{$resource->full_name}}</a>
                </td>
            @else
                <td>{{$resource->full_name}}</td>
            @endif
            <td>{{$resource->email}}</td>
            <td>{{$resource->present()->upcomingAllocationText()}}</td>
        </tr>
    @endforeach
@endif
