@extends('layouts.master')
@section('styles')
    <link href="{{asset_versioned('/css/plugins/chosen/chosen.css')}}" rel="stylesheet">
@endsection
@section('content')
    <div id="page-wrapper">
        <div class="container-fluid">
            <!-- Page Heading -->
            <div class="row">
                <div class="col-lg-12">
                    <ol class="breadcrumb">
                        <li class="active">
                            <i class="fa fa-tasks"></i> Projects
                        </li>
                    </ol>
                </div>
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <h2>Project Lists</h2>

                    <div class="table-responsive white-block">
                        @include('layouts.notifications')
                        <div class="white-block">

                          <form id="project-search-form" class="project-search-form" method="GET" action="{{route('project.ajax.filter')}}">
                            <div class="row">
                              <div class="col-md-3">
                                <input id="project-search-input" @if(isset($search)) value="{{$search}}"@endif name="pname" type="text" class="project-search form-control" placeholder="Search project" />
                              </div>
                              <div class="col-md-3">
                                <select name="skills[]" multiple id="project-skill-filter" class="form-control" data-placeholder="Filter by skills">
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




                            </div>

                          </form>
                        </div>
                        <div class="white-block projects-listing-table" id="projects-listing-table">
                          <table class="table table-hover">
                              <thead>
                              <tr>
                                  <th>Project Name</th>
                                  <th>Project Code</th>
                                  <th>Skills</th>
                                  <th>Status</th>
                                  <th width="300px">Action</th>
                              </tr>
                              </thead>
                              <tbody>
                              @if(count($projects)==0)
                                  <tr>
                                      <th colspan="5">No projects found</th>
                                  </tr>
                              @endif
                              @foreach($projects as $key => $project)
                                  @include( 'projects.partials.project-table-row', array( 'project' => $project ) )
                              @endforeach
                              </tbody>
                          </table>
                        {!! $projects->render() !!}
                      </div>

                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- /#page-wrapper -->
@endsection
@section('script')
    <script src="{{asset_versioned('/js/chosen.jquery.js')}}"></script>
    <script>
        $(document).ready(function () {
            $(document).on( "click", ".project-delete-btn", function() {
                var btn = $(this);
                bootbox.confirm({
                    message: "Are you sure you want to delete this project?<br>Please note that, all allocations and associated data will be deleted",
                    callback: function (result) {
                        if (result) {
                            btn.closest('form.project-delete-form').submit();
                        }
                    }
                });
            });
        });
    </script>
    <script>
    function projectSearch()
    {
      $searchForm = $('#project-search-form');
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
                 $('#projects-listing-table').html(result.html)
               }
           });
    }
    $(document).ready(function(){

        $('#project-search-input').keyup(function(){delayFunction(function(){
                projectSearch();
            }, 800 );
        });
        $("#project-skill-filter").chosen({
            no_results_text: "Oops, nothing found with search: ",
        }).change(function() {
            projectSearch();
        });

      });
      $(function() {
          $('body').on('click', '.ajaxpagination a', function(e) {
              e.preventDefault();

              var url = $(this).attr('href');
              getArticles(url);
              window.history.pushState("", "", url);
          });

          function getArticles(url) {
              $.ajax({
                  url : url
              }).done(function (data) {
                  $('#projects-listing-table').html(data.html);
              })
          }
      });
      </script>
@endsection
