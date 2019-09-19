@extends('layouts.master')
@section('styles')
@endsection
@section('content')
<div id="page-wrapper">
    <div class="container-fluid">
      <div class="container-fluid">
          <div class="row">
              <div class="col-lg-12">
                  <ol class="breadcrumb">
                      <li>
                          <i class="fa fa-users"></i> <a href="{{ route('resources.index') }}">Resources</a>
                      </li>
                      <li class="active">
                          <i class="fa fa-user"></i> {{$user->profile->first_name}} {{$user->profile->last_name}}
                      </li>
                  </ol>
              </div>
          </div>
          <div class="row">
              <div class="col-lg-12 margin-tb">
                  <div class="pull-left">
                      <h2>{{$user->profile->first_name}} {{$user->profile->last_name}}</h2>
                  </div>
              </div>
          </div>
        <div class="row white-block">
                  <table class="table table-user-information">
                    <tbody>
                      <tr>
                        <td><b>First Name:</b></td>
                        <td>{{$user->profile->first_name}}</td>
                      </tr>
                      <tr>
                        <td><b>Last Name:</b></td>
                        <td>{{$user->profile->last_name}}</td>
                      </tr>
                      <tr>
                        <td><b>Email:</b></td>
                        <td>{{$user->email}}</td>
                      </tr>

                      <tr>
                        <td><b>Role:</b></td>
                        <td>
                          @foreach ($userRole as $roll)
                            {{ $roll->display_name }}
                          @endforeach
                        </td>
                      </tr>
                      <tr>
                        <td><b>Company:</b></td>
                        <td>{{ $user->profile->company }}</td>
                      </tr>
                      <tr>
                        <td><b>Designation:</b></td>
                        <td>{{ $designation->name }}</td>
                      </tr>
                      <tr>
                        <td><b>Skills:</b></td>
                        <td>
                          @foreach ($skills as $skill)
                          <label class="label label-success">
                            {{ $skill->label }}
                          </label>
                          @endforeach
                        </td>
                      </tr>
                      <tr>
                        <td><b>Address Line 1:</b></td>
                        <td>{{ $user->profile->address_line1 }}</td>
                      </tr>
                        <td><b>Address Line 2:</b></td>
                        <td>{{ $user->profile->address_line2 }}</td>
                      </tr>
                      </tr>
                        <td><b>Phone:</b></td>
                        <td>{{ $user->profile->phone }}</td>
                      </tr>
                      </tr>
                        <td><b>City:</b></td>
                        <td>{{ $user->profile->city }}</td>
                      </tr>
                      </tr>
                        <td><b>State:</b></td>
                        <td>{{ $user->profile->state }}</td>
                      </tr>
                      </tr>
                        <td><b>Country:</b></td>
                        <td>{{ $user->profile->country }}</td>
                      </tr>
                      </tr>
                        <td><b>Zipcode:</b></td>
                        <td>{{ $user->profile->zipcode }}</td>
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
