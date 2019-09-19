@extends('layouts.master')

@section('content')
<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-10 col-md-offset-1">
				@if(isset($message))
				<div class="well">{{$message}}</div>
				@else
				<div class="well">You don't have permission to access this page</div>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection
