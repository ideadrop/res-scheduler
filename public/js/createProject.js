function resetProjectCreateForm(){
    setTimeout(function(){
        $('#newProject input').val('');
        $('#newProject textarea').val('');
        $('#project-manager').tokenfield('setTokens', []);
        $('#resources').tokenfield('setTokens', []);
        $('#project-tags').tokenfield('setTokens', []);
        $('#project-skills').tokenfield('setTokens', []);
        $('.rss-alert').remove();
    },200);
}
$(document).ready(function () {
    $('#newProject').on('hidden.bs.modal', function () {
        resetProjectCreateForm();
    });
    $('#newProject').on('shown.bs.modal', function () {
        $('#newProject #start-date').datepicker({
            container: "#newProject .modal-body",
            autoclose: true,
            daysOfWeekDisabled: "0,6"
        }).on('changeDate', function (e) {
            $('#newProject #end-date').datepicker('clearDates');
            $('#newProject #end-date').datepicker('setStartDate', $('#newProject #start-date').datepicker('getDate'));
        });

        $('#newProject #end-date').datepicker({
            container: "#newProject .modal-body",
            autoclose: true,
            daysOfWeekDisabled: "0,6"
        });

        var tokenDataEngine1 = new Bloodhound({
            remote: {
                url: "/getTags?query=%QUERY",
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

        tokenDataEngine1.initialize();

        $('input#project-tags').tokenfield({
            createTokensOnBlur: true,
            typeahead: [null, {
                    name: "project-tags",
                    templates: {
                        suggestion: function (data) {
                            return '<div>#' + data.label + '</div>';
                        }
                    },
                    display: 'label',
                    source: tokenDataEngine1.ttAdapter()
                }],
            beautify: false,
            delimiter: [','],
            showAutocompleteOnFocus: false
        }).on('tokenfield:createtoken', function (e) {
            var re = /^[a-zA-Z0-9]*$/;
            var valid = re.test(e.attrs.name);
            if (!valid) {
                e.preventDefault();
                $('.tag-error-message').remove();
                $(this).parent().append('<span class="help-block alert-danger tag-error-message">special character is not allowed</span>');
            }
            var existingTokens = $(this).tokenfield('getTokens');
            $.each(existingTokens, function (index, token) {
                if (token.value === e.attrs.value)
                    e.preventDefault();
            });
        });

        var feedRequest = false;
        $('input#project-manager').tokenfield({
            typeahead: [null, {
                    name: "project-manager",
                    templates: {
                        suggestion: function (data) {
                            return '<div>' + data.label + '</div>';
                        }
                    },
                    source: function (request, response) {

                        var existingTokens = $('input#project-manager').tokenfield('getTokens');

                        if(existingTokens.length>0){
                            response([]);
                        }else{
                            if (feedRequest) {
                                feedRequest.abort();
                            }
                            feedRequest = $.get("/getManagers", {
                                query: request
                            }, function (data) {
                                //data = $.parseJSON(data);
                                response(data.managers);
                            });
                        }

                    },
                    display: 'label',
                }],
            beautify: false,
            delimiter: [','],
            limit: 1,
            disableEdit: true,
            createTokensOnBlur:false,
            showAutocompleteOnFocus: false
        }).on('tokenfield:edittoken', function (e) {
            e.preventDefault();
            return false;
        }).on('tokenfield:createtoken', function (e) {

            if(e.attrs.value=='' || isNaN(e.attrs.value)){
                e.preventDefault();return false;
            }
            var existingTokens = $(this).tokenfield('getTokens');
            $.each(existingTokens, function (index, token) {
                if (token.value === e.attrs.value) {
                    e.preventDefault();
                }
            });
        });

        var getResourcesRequest = false;
        $('input#resources').tokenfield({
            typeahead: [null, {
                    name: "resources",
                    templates: {
                        suggestion: function (data) {
                            return '<div>' + data.label + '</div>';
                        }
                    },
                    source: function (request, response) {

                        if (getResourcesRequest) {
                            getResourcesRequest.abort();
                        }
                        getResourcesRequest = $.get("/getDevelopers", {
                            query: request,
                            selected: $('input#resources').val()
                        }, function (data) {
                            //data = $.parseJSON(data);
                            response(data.devs);
                        });

                    },
                    display: 'label',
                }],
            beautify: false,
            delimiter: [','],
            showAutocompleteOnFocus: false
        }).on('tokenfield:createtoken', function (e) {

            if(e.attrs.value=='' || isNaN(e.attrs.value)){
                e.preventDefault();return false;
            }
            var existingTokens = $(this).tokenfield('getTokens');
            $.each(existingTokens, function (index, token) {
                if (token.value === e.attrs.value)
                    e.preventDefault();
            });

        });
        
        var tokenDataEngine5 = new Bloodhound({
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

        tokenDataEngine5.initialize();

        $('input#project-skills').tokenfield({
            createTokensOnBlur: true,
            typeahead: [null, {
                    name: "project-skills",
                    templates: {
                        suggestion: function (data) {
                            return '<div>' + data.label + '</div>';
                        }
                    },
                    display: 'label',
                    source: tokenDataEngine5.ttAdapter()
                }],
            beautify: false,
            delimiter: [','],
            showAutocompleteOnFocus: false
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


    });

    $('body').on('submit', '#addProjectFrm', function (e) {
        e.preventDefault();
        var submitBtn = $('#add-project-btn');

        var thisForm = $(this);

        setTimeout(function () {
            submitBtn.val('Please wait..').prop( "disabled", true );
        }, 100);

        var errBag = [];
        if ($('#project-title').val().replace(/ /g, "") == '') {
            errBag.push('Project title should not be empty');
        }
        if ($('#project-code').val().replace(/ /g, "") == '') {
            errBag.push('Project code should not be empty');
        }
        if ($('#project-manager').val().replace(/ /g, "") == '') {
            errBag.push('Please select a project manager');
        }
        /*if ($('#resources').val().replace(/ /g, "") == '') {
            errBag.push('Please select project resources');
        }*/

        var start = $('#start-date').val();
        var end = $('#end-date').val();
        if(start==''){
            errBag.push('Please select project start date');
        }
        if(end==''){
            errBag.push('Please select project end date');
        }
        if( (start!='') && (end!='')) {
            if(new Date(start) >= new Date(end)){
                errBag.push('Project start date should not be greater than end date');
            }
        }
        if (errBag.length > 0){
            setTimeout(function () {
                submitBtn.val(submitBtn.attr('placeholder')).prop( "disabled", false );
            }, 100);
            var errMsg = '<h4>Validation Failed</h4>';
            errBag.forEach(function(item,index){
                errMsg+=item+"<br>";
            });
            iddMessage({
                message: errMsg,
                status: 'danger',
                selector: '#addProjectFrm .modal-footer .submit'
            });
        }else{
            $.ajax({
                url:thisForm.attr('action'),
                method:thisForm.attr('method'),
                data:thisForm.serialize(),
                async:false,
                success:function(response){

                    setTimeout(function () {
                        submitBtn.val(submitBtn.attr('placeholder')).prop( "disabled", false );
                    }, 100);

                    if(response.status=='confirm'){
                        bootbox.confirm({
                            title:"Are you sure you want continue with current resources?",
                            message: response.message,
                            callback: function (result) {
                                if (result == true) {
                                    $('#create-resource-confirm').val("1");
                                    thisForm.submit();
                                }
                            }
                        });

                    }else if(response.status=='success'){
                        $('#newProject').modal('hide');
                        resetProjectCreateForm();
                        $.notify({
                            message: 'Project Successfully Created'
                        }, {type: 'success'});
                        redirectTo(response.redirect_url);
                    }else if(response.status=='error'){
                        iddMessage({
                            message: response.message,
                            status: 'danger',
                            selector: '#addProjectFrm .modal-footer .submit'
                        });
                    }
                }
            });
        }

    });

    $('body').on('change', '#addProjectFrm input', function (e) {
        var hasError = false;
        if ($(this).val() != '') {
            if ($(this).attr('id') != 'start-date' && $(this).attr('id') != 'start-date') {
                $(this).parent().removeClass('has-error');
            } else {
                $(this).parent().parent().removeClass('has-error');
            }
        }

    });


});