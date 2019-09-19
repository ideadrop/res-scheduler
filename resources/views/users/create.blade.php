@extends('layouts.master') @section('content')
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
                    <h2>Create New Resource</h2>
                </div>
                <div class="pull-right">
                    <a class="btn btn-primary" href="{{ route('resources.index') }}"> Back</a>
                </div>
            </div>
        </div>
        @include('layouts.notifications')

        {!! Form::open(['route' => 'resources.store','method'=>'POST', 'class' => 'form-horizontal']) !!}
        <div class="row">
            <div class="col-md-12">
                <div class="white-block">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">First Name <span class="field-req">*</span></label>
                                <div class="col-sm-9">
                                    {!! Form::text('first_name', null, array('placeholder' => 'First Name','class' => 'form-control')) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Last Name <span class="field-req">*</span></label>
                                <div class="col-sm-9">
                                    {!! Form::text('last_name', null, array('placeholder' => 'Last Name','class' => 'form-control')) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Email <span class="field-req">*</span></label>
                                <div class="col-sm-9">
                                    {!! Form::text('email', null, array('placeholder' => 'Email','class' => 'form-control')) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Password <span class="field-req">*</span></label>
                                <div class="col-sm-9">
                                    {!! Form::password('password', array('placeholder' => 'Password','class' => 'form-control')) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Confirm Password <span class="field-req">*</span></label>
                                <div class="col-sm-9">
                                    {!! Form::password('confirm-password', array('placeholder' => 'Confirm Password','class' => 'form-control')) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Role <span class="field-req">*</span></label>
                                <div class="col-sm-9">
                                    {!! Form::select('role', $roles,[], array('placeholder' => 'Select Role','class' => 'form-control')) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Allocatable <span class="field-req">*</span></label>
                                <div class="col-sm-9">
                                    {!! Form::select('allocatable', ['1'=>'YES','0'=>'NO'], '1' , array('placeholder' => 'Select Allocatable','class' => 'form-control')) !!}</div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Company <span class="field-req">*</span></label>
                                <div class="col-sm-9">
                                    {!! Form::text('company', null, array('placeholder' => 'Company','class' => 'form-control')) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Designation <span class="field-req">*</span></label>
                                <div class="col-sm-9">
                                    {!! Form::select('user_type_id', $userTypes,[], array('placeholder' => 'Select designation','class' => 'form-control')) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Skills</label>
                                <div class="col-sm-9">
                                    {!! Form::text('user-skills', null, array('placeholder' => '','class' => 'form-control','id'=>'user-skills')) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Address Line 1 <span class="field-req">*</span></label>
                                <div class="col-sm-9">
                                    {!! Form::text('address_line1', null, array('placeholder' => 'Address Line 1','class' => 'form-control')) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Address Line 2</label>
                                <div class="col-sm-9">
                                    {!! Form::text('address_line2', null, array('placeholder' => 'Address Line 2','class' => 'form-control')) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Phone <span class="field-req">*</span></label>
                                <div class="col-sm-9">
                                    {!! Form::text('phone', null, array('placeholder' => 'Phone','class' => 'form-control')) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">City</label>
                                <div class="col-sm-9">
                                    {!! Form::text('city', null, array('placeholder' => 'City','class' => 'form-control')) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">State</label>
                                <div class="col-sm-9">
                                    {!! Form::text('state', null, array('placeholder' => 'State','class' => 'form-control')) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Country</label>
                                <div class="col-sm-9">
                                    {!! Form::text('country', null, array('placeholder' => 'Country','class' => 'form-control')) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Zipcode</label>
                                <div class="col-sm-9">
                                    {!! Form::text('zipcode', null, array('placeholder' => 'Zipcode','class' => 'form-control')) !!}
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="submit-blk clearfix">
                        <div class="row">
                            <div class="col-xs-10 col-sm-10 col-md-9 col-md-offset-3">
                                <button type="submit" class="btn btn-primary">Save Resource</button>
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