@extends('layouts.master')

@section('content')
    <div id="page-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <h2>Role Management</h2>
                    </div>
                    <div class="pull-right">
                        @permission('role-create')
                        <a class="btn btn-success" href="{{ route('roles.create') }}"> Create New Role</a>
                        @endpermission
                    </div>
                </div>
            </div>
            @include('layouts.notifications')
            <div class="white-block">

              <form id="role-search-form" class="role-search-form" method="GET" action="{{route('role.ajax.filter')}}">
                <div class="row">
                  <div class="col-md-3">
                    <input id="role-search-input" @if(isset($search)) value="{{$search}}"@endif name="rname" type="text" class="role-search form-control" placeholder="Search role" />
                  </div>
                </div>
              </form>
            </div>
            <div class="white-block roles-listing-block" id="roles-listing-block">
                <table class="table table-hover">
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th width="280px">Action</th>
                    </tr>
                    @foreach ($roles as $key => $role)
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
                    @endforeach
                </table>
                {!! $roles->render() !!}
            </div>

        </div>
    </div>
@endsection
@section('script')
    <script>
    function roleSearch()
    {
      $searchForm = $('#role-search-form');
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
                 $('#roles-listing-block').html(result.html)
               }
           });
    }
        $(document).ready(function () {
            $(document).on( "click", ".role-delete-btn", function() {
                var btn = $(this);
                bootbox.confirm({
                    message: "Are you sure you want to delete this role?",
                    callback: function (result) {
                        if (result) {
                            btn.closest('form.role-delete-form').submit();
                        }
                    }
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
                        $('#roles-listing-block').html(data.html);
                    })
                }
            });
        });
        $('#role-search-input').keyup(function(){delayFunction(function(){
                roleSearch();
            }, 800 );
        });
    </script>
@endsection
