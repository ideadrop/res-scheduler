@if(count($projects)==0)
    <tr>
        <td colspan="3">
            <div class="well text-center"> No Active Projects</div>
        </td>
    </tr>
@else:
    @foreach($projects as $index=>$project)
        <tr>
            <td>{{$index+1}}</td>
            @if($authUser->can('project-list-allocate'))
                <td><a href="{{ route('project.view',$project->id) }}" title="View Project Calender">{{$project->name}}</a></td>
            @else
                <td>{{$project->name}}</td>
            @endif
            <td>{{$project->project_code}}</td>
        </tr>
    @endforeach
@endif
