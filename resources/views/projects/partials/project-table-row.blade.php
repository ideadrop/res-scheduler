<tr>
    <td><a href="{{ route('project.view',$project->id) }}">{{$project->name}}</a></td>
    <td>{{$project->project_code}}</td>
    <td>
        @if(!empty($project->skills))
        @foreach($project->skills as $skill)
        <label class="label label-success">{{ $skill->skill->name }}</label>
        @endforeach
        @endif
    </td>
    <td>Active</td>
    <td class="text-right">
        @if(Auth::user()->can(['project-list-allocate']))
            <a class="btn btn-info" href="{{ route('project.show',$project->id) }}">Timeline</a>
        @endif
        @if(Auth::user()->can(['project-edit']))
            <a data-toggle="modal" data-id="{{$project->id}}"
               data-target="#editProject" class="btn btn-primary openProjectEdit"
               href="">Edit</a>
        @endif
        @if(Auth::user()->can(['project-delete']))
            {!! Form::open(['class'=>'project-delete-form','method' => 'DELETE','route' => ['project.destroy', $project->id],'style'=>'display:inline']) !!}
            {!! Form::button('Delete', ['class' => 'btn btn-danger project-delete-btn']) !!}
            {!! Form::close() !!}
        @endif
    </td>
</tr>
