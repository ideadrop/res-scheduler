@extends('layouts.master')
@section('styles')
@endsection
@section('content')
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <ol class="breadcrumb">
                    <li>
                        <i class="fa fa-list"></i> <a href="{{ route('project.list') }}">Projects</a>
                    </li>
                    <li class="active">
                        <i class="fa fa-users"></i> {{$project->name}}
                    </li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2> {{ $project->name }} </h2>
                </div>
                <div class="pull-right">
                    <a class="btn btn-primary" href="{{ route('project.show', ['id'=> $project->id]) }}"> Timeline</a>
                </div>
            </div>
        </div>
        <div class="row white-block">
                  <table class="table table-user-information">
                    <tbody>
                      <tr>
                        <td><b>Project Title:</b></td>
                        <td>{{ $project->name }}</td>
                      </tr>
                      <tr>
                        <td><b>Project Code:</b></td>
                        <td>{{ $project->project_code }}</td>
                      </tr>
                      <tr>
                        <td><b>Project Manager:</b></td>
                        <td>
                          @if($project_manager)
                            <a href="{{route('resources.profile', ["id"=>$project_manager->value])}}">
                              {{ $project_manager->label}}
                            </a>
                          @endif
                        </td>
                      </tr>

                      <tr>
                        <td><b>Resources:</b></td>
                        <td>
                          @foreach ($developers as $developer)
                          <a href="{{route('resources.profile', ["id"=>$developer->value])}}">
                            {{ $developer->label }}
                          </a><br/>
                          @endforeach
                        </td>
                      </tr>
                      <tr>
                        <td><b>Start Date:</b></td>
                        <td>{{ $project->start_date }}</td>
                      </tr>
                      <tr>
                        <td><b>End Date:</b></td>
                        <td>{{ $project->end_date }}</td>
                      </tr>
                      <tr>
                        <td><b>Project Notes:</b></td>
                        <td>@if($note)
                          {{ $note->value }}
                          @endif
                        </td>
                      </tr>
                        <td><b>Tags:</b></td>
                        <td>
                          @foreach ($tags as $tag)
                          <label class="label label-success">
                            {{ $tag->label }}
                          </label>
                          @endforeach
                        </td>
                      </tr>
                    </tr>
                      <td><b>Skills:</b></td>
                      <td>
                        @foreach ($skills as $skill)
                        <label class="label label-success">
                          {{ $skill->label }}
                        </label>
                        @endforeach
                      </td>
                    </tr>

                    </tbody>
                  </table>

                  <!-- <a href="#" class="btn btn-primary">My Sales Performance</a>
                  <a href="#" class="btn btn-primary">Team Sales Performance</a> -->
                </div>
    </div>
</div>
@endsection
@section('script')
@endsection
