
@extends('layouts.master')
@section('styles')
    <link href="{{asset_versioned('/css/plugins/chosen/chosen.css')}}" rel="stylesheet">
@endsection
@section('content')
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <ol class="breadcrumb">
                    <li class="active">
                        <i class="fa fa-users"></i> Resources
                    </li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>Resources Management</h2>
                </div>
                <div class="pull-right">
                    <a class="btn btn-success" href="{{ route('resources.create') }}"> Create New Resource</a>
                </div>
            </div>
        </div>
        @include('layouts.notifications')
        <div class="white-block">

          <form id="resource-search-form" class="resource-search-form" method="GET" action="{{route('resource.ajax.filter')}}">
            <div class="row">
              <div class="col-md-3">
                <input id="resource-search-input" @if(isset($search)) value="{{$search}}"@endif name="uname" type="text" class="resource-search form-control" placeholder="Search username or email" />
              </div>
              <div class="col-md-3">
                <select name="skills[]" multiple id="resource-skill-filter" class="form-control" data-placeholder="Filter by skills">
                  @foreach($skills as $skill)
                  <option value="{{$skill->id}}"
                    @if(isset($inputSkills))
                    @if(in_array($skill->id, $inputSkills))
                      selected
                    @endif
                    @endif
                    >{{$skill->name}}</option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-3">
                <select name="roles[]" multiple id="resource-role-filter" class="form-control" data-placeholder="Filter by roles">
                  @foreach($roles as $role)
                  <option value="{{$role->id}}"
                    @if(isset($inputRoles))
                    @if(in_array($role->id, $inputRoles))
                      selected
                    @endif
                    @endif
                    >{{$role->display_name}}</option>
                  @endforeach

                </select>
              </div>
              <div class="col-md-3">
                <input id="resource-availablity" type="checkbox" name="available" value="1" @if(isset($isAvailable)) @if($isAvailable == 1) checked @endif @endif><label>Available Today</label>
              </div>

            </div>

          </form>
        </div>
        <div class="white-block users-listing-table" id="users-listing-table">
          <table class="table table-hover">
              <tr>
                  <th>Name</th>
                  <th>Today's allocations</th>
                  <th>Skills</th>
                  <th>Roles</th>
                  <th width="300px">Action</th>
              </tr>
              @if(count($users)==0)
                  <tr>
                      <th colspan="5">No Resources found</th>
                  </tr>
              @endif
              @foreach ($users as $key => $user)
              @include( 'users.partials.resource-table-row', array( 'user' => $user ) )
              @endforeach
          </table>
        {!! $users->render() !!}
        </div>

    </div>
</div>

@endsection
@section('script')
<script src="{{asset_versioned('/js/chosen.jquery.js')}}"></script>
<script>
function resourceSearch()
{
  $searchForm = $('#resource-search-form');
  url = $searchForm.attr('action');
  data = $searchForm.serialize();
  window.history.pushState("", "", url +'?'+ data);
  $.ajax({
           method: "GET",
           url: url,
           data: data,
           dataType: "JSON",
           cache: false,
           success: function(result) {
             $('#users-listing-table').html(result.html)
           }
       });
}
$(document).ready(function(){

    $('#resource-search-input').keyup(function(){delayFunction(function(){
            resourceSearch();
        }, 800 );
    });
    $('#resource-availablity').change(function(){
      resourceSearch();
    })
    $("#resource-skill-filter").chosen({
        no_results_text: "Oops, nothing found with search: ",
    }).change(function() {
        resourceSearch();
    });

   $("#resource-role-filter").chosen({
       no_results_text: "Oops, nothing found with search: ",
   }).change(function() {
       resourceSearch();
   });

   $(function() {
       $('body').on('click', '.ajaxpagination a', function(e) {
           e.preventDefault();

           var url = $(this).attr('href');
           getContent(url);
           window.history.pushState("", "", url);
       });

       function getContent(url) {
           $.ajax({
               url : url
           }).done(function (data) {
               $('#users-listing-table').html(data.html);
           })
       }
   });

  });
  </script>
  @endsection
