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
                        <i class="fa fa-user"></i> {{$user->profile->first_name}} {{$user->profile->last_name}}
                    </li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>Edit Resource</h2>
                </div>
                <div class="pull-right">
                    <a class="btn btn-primary" href="{{ route('resources.index') }}"> Back</a>
                </div>
            </div>
        </div>
        @include('layouts.notifications')
        {!! Form::model($user, ['method' => 'PATCH', 'class' => 'form-horizontal', 'route' => ['resources.update', $user->id]]) !!}
        <div class="row">
            <div class="col-md-12">
                <div class="white-block">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">First Name <span class="field-req">*</span></label>
                                <div class="col-sm-9">
                                    {!! Form::text('first_name', $user->profile->first_name, array('placeholder' => 'First Name','class' => 'form-control')) !!}</div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Last Name <span class="field-req">*</span></label>
                                <div class="col-sm-9">
                                    {!! Form::text('last_name', $user->profile->last_name, array('placeholder' => 'Last Name','class' => 'form-control')) !!}</div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Email <span class="field-req">*</span></label>
                                <div class="col-sm-9">
                                    {!! Form::text('email', $user->email, array('placeholder' => 'Email','class' => 'form-control')) !!}</div>
                            </div>
                        </div>
                        <!--         <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Password:</label><div class="col-sm-10">
                            {!! Form::password('password', array('placeholder' => 'Password','class' => 'form-control')) !!}</div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Confirm Password:</label><div class="col-sm-10">
                            {!! Form::password('confirm-password', array('placeholder' => 'Confirm Password','class' => 'form-control')) !!}</div>
                        </div>
                    </div> -->
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Role <span class="field-req">*</span></label>
                                <div class="col-sm-9">
                                    {!! Form::select('role', $roles, $userRole, array('placeholder' => 'Select Role','class' => 'form-control')) !!}</div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Allocatable <span class="field-req">*</span></label>
                                <div class="col-sm-9">
                                    {!! Form::select('allocatable', ['1'=>'YES','0'=>'NO'], $user->allocatable, array('placeholder' => 'Select Allocatable','class' => 'form-control')) !!}</div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Company <span class="field-req">*</span></label>
                                <div class="col-sm-9">
                                    {!! Form::text('company', $user->profile->company, array('placeholder' => 'Company','class' => 'form-control')) !!}</div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Designation <span class="field-req">*</span></label>
                                <div class="col-sm-9">
                                    {!! Form::select('designation', $userTypes, $user->profile->designation, array('placeholder' => 'Select Designation','class' => 'form-control')) !!}
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
                                    {!! Form::text('address_line1', $user->profile->address_line1, array('placeholder' => 'Address Line 1','class' => 'form-control')) !!}</div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Address Line 2</label>
                                <div class="col-sm-9">
                                    {!! Form::text('address_line2', $user->profile->address_line2, array('placeholder' => 'Address Line 2','class' => 'form-control')) !!}</div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Phone <span class="field-req">*</span></label>
                                <div class="col-sm-9">
                                    {!! Form::text('phone', $user->profile->phone, array('placeholder' => 'Phone','class' => 'form-control')) !!}</div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">City</label>
                                <div class="col-sm-9">
                                    {!! Form::text('city', $user->profile->city, array('placeholder' => 'City','class' => 'form-control')) !!}</div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">State</label>
                                <div class="col-sm-9">
                                    {!! Form::text('state', $user->profile->state, array('placeholder' => 'State','class' => 'form-control')) !!}</div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Country</label>
                                <div class="col-sm-9">
                                    {!! Form::text('country', $user->profile->country, array('placeholder' => 'Country','class' => 'form-control')) !!}</div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Zipcode</label>
                                <div class="col-sm-9">
                                    {!! Form::text('zipcode', $user->profile->zipcode, array('placeholder' => 'Zipcode','class' => 'form-control')) !!}
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="submit-blk clearfix">
                        <div class="row">
                        <div class="col-xs-10 col-sm-10 col-md-9 col-md-offset-3">
                            <button type="submit" class="btn btn-primary">Update Resource</button>
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
<script>
    $(document).ready(function () {
        var tokenDataEngine = new Bloodhound({
            remote: {
                url: "/getSkills?query=%QUERY",
                wildcard: "%QUERY",
                filter: function (response) {
                    return response.items;
                }
            },
            datumTokenizer: function (tag) {
                //            console.log(d);
                return Bloodhound.tokenizers.whitespace(tag.label);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace
        });

        tokenDataEngine.initialize();

        $('input#user-skills').tokenfield({
            createTokensOnBlur: true,
            typeahead: [null, {
                name: "user-skills",
                templates: {
                    suggestion: function (data) {
                        return '<div>' + data.label + '</div>';
                    }
                },
                display: 'label',
                source: tokenDataEngine.ttAdapter()
            }],
            beautify: false,
            delimiter: [','],
            showAutocompleteOnFocus: true
        }).on('tokenfield:createtoken', function (e) {
            var re = /^[a-zA-Z0-9]*$/;
            var valid = re.test(e.attrs.name);
            if (!valid) {
                e.preventDefault();
                $('.skill-error-message').remove();
                $(this).parent().append('<span class="help-block alert-danger skill-error-message">special character is not allowed</span>');
            }
            var existingTokens = $(this).tokenfield('getTokens');
            $.each(existingTokens, function (index, token) {
                if (token.value === e.attrs.value)
                    e.preventDefault();
            });
        });

        $.ajax({
            url: "/resources/getskills/{{$user->id}}",
            type: 'GET',
            dataType: 'json',
            beforeSend: function (request) {
                return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
            },
            success: function (response) {
                $('input#user-skills').tokenfield('setTokens', response.skills);
            }
        });
    });
</script>

@endsection