@extends('layouts.master')

@section('content')
<div id="page-wrapper">
    <div class="container-fluid">
	<div class="row">
	    <div class="col-lg-12 margin-tb">
	        <div class="pull-left">
	            <h2> Show Role</h2>
	        </div>
	        <div class="pull-right">
	            <a class="btn btn-primary" href="{{ route('roles.index') }}"> Back</a>
	        </div>
	    </div>
	</div>
	<div class="white-block">
	    <div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Name:</strong>
                {{ $role->display_name }}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Description:</strong>
                {{ $role->description }}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Permissions:</strong>

                @if(!empty($allPermissions))
					@foreach($allPermissions as $v)

            <div class="row">
              <div class="col-md-4">
                <span>{{ $v->display_name }}</span>
              </div>
              <div class="col-md-4">
                @if($rolePermissions->contains('id', $v->id))
                  <i class="fa fa-check" aria-hidden="true" style="color:green"></i>
                @else
                  <i class="fa fa-times" aria-hidden="true" style="color:red"></i>
                @endif
              </div>
            </div>

					@endforeach
				@endif
            </div>
        </div>
	</div>
	</div>
    </div>
    </div>
@endsection
