@extends('layouts.master')
@section('content')
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <ol class="breadcrumb">
                    <li>
                        <i class="fa fa-users"></i> <a href="{{ route('resources.index') }}">Resources</a>
                    </li>
                    <li class="active">
                        <i class="fa fa-user"></i> Add Resource
                    </li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>CSV import resources</h2>
                </div>
                <div class="pull-right">
                    <a class="btn btn-primary" href="{{ route('resources.index') }}"> Back</a>
                </div>
            </div>
        </div>
        @include('layouts.notifications')

        {!! Form::open(['route' => 'resources.csv.upload','method'=>'POST', 'class' => 'form-horizontal', 'enctype'=>'multipart/form-data', 'autocomplete'=>'off']) !!}
        <div class="row" title='Please note: Resources uploaded via CSV will be of role "Developer" and Designation as "Software Engineer"'>
            <div class="col-md-12">
                <div class="white-block">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label class="col-sm-5 control-label">Upload Resource CSV (<a href="{{asset('/sample.csv')}}" style="color:#737373;">View Sample</a>)<span class="field-req">*</span></label>
                                <div class="col-sm-7">
                                    {!! Form::file('file', null, array('placeholder' => 'Select CSV','class' => 'form-control')) !!}
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="submit-blk clearfix">
                        <div class="row">
                            <div class="col-xs-10 col-sm-10 col-md-9 col-md-offset-3">
                                <button type="submit" class="btn btn-primary">Upload Resources</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>
@endsection @section('script')
<script src="{{asset_versioned('/js/createResource.js')}}"></script>
@endsection