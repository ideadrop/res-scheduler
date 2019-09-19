function resetProjectEditForm(){
    setTimeout(function() {
        $('#editProject input').val('');
        $('#editProject textarea').val('');
        $('#edit-project-manager').tokenfield('setTokens', []);
        $('#edit-resources').tokenfield('setTokens', []);
        $('#edit-project-tags').tokenfield('setTokens', []);
        $('#edit-project-skills').tokenfield('setTokens', []);
        $('.rss-alert').remove();
    },100);
}

$(document).ready(function () {

    $('body').on("click", ".openProjectEdit", function () {
        var projectId = $(this).data('id');
        $("#editProjectFrm #edit_project_id").val(projectId);
    });

    $('#editProject').on('hidden.bs.modal', function () {
        resetProjectEditForm();
    });
    $('#editProject').on('shown.bs.modal', function () {

        $.ajax({
            url: "/project/edit/" + $('#edit_project_id').val(),
            type: 'GET',
            dataType: 'json',
            async:false,
            beforeSend: function (request) {
                return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
            },
            success: function (response) {
                if (response.status == 'success') {
                    $('#editProjectFrm #edit-project-title').val(response.project.name);
                    $('#editProjectFrm #edit-project-code').val(response.project.project_code);
                    $('#editProjectFrm #edit-start-date').val(response.project.start_date);
                    $('#editProjectFrm #edit-end-date').val(response.project.end_date);
                    var startDatePicker = $('#edit-start-date').datepicker({
                        container: "#editProject .modal-body",
                        autoclose: true,
                        daysOfWeekDisabled: "0,6"
                    }).on('changeDate', function (e) {
                        $('#edit-end-date').datepicker('clearDates');
                        $('#edit-end-date').datepicker('setStartDate', $('#edit-start-date').datepicker('getDate'));
                    });

                    var endDatePicker = $('#edit-end-date').datepicker({
                        container: "#editProject .modal-body",
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

                    $('input#edit-project-tags').tokenfield({
                        createTokensOnBlur: true,
                        typeahead: [null, {
                                name: "edit-project-tags",
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

                    $('input#edit-project-tags').tokenfield('setTokens', response.tags);

                    var tokenDataEngine2 = new Bloodhound({
                        remote: {
                            url: "/getManagers?query=%QUERY",
                            wildcard: "%QUERY",
                            filter: function (response) {
                                return response.managers;
                            }
                        },
                        datumTokenizer: function (d) {
//            console.log(d);
                            return Bloodhound.tokenizers.whitespace(d.label);
                        },
                        queryTokenizer: Bloodhound.tokenizers.whitespace
                    });

                    tokenDataEngine2.initialize();
                    var getMangersEditRequest = false;
                    $('input#edit-project-manager').tokenfield({
                        typeahead: [null, {
                                name: "edit-project-manager",
                                templates: {
                                    suggestion: function (data) {
                                        return '<div>' + data.label + '</div>';
                                    }
                                },
                                /*source: tokenDataEngine2.ttAdapter(),*/
                                source: function (request, response) {

                                    var existingTokens = $('input#edit-project-manager').tokenfield('getTokens');

                                    if(existingTokens.length>0){
                                        response([]);
                                    }else{
                                        if (getMangersEditRequest) {
                                            getMangersEditRequest.abort();
                                        }
                                        getMangersEditRequest = $.get("/getManagers", {
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

                    if (response.project_manager != null) {
                        var result = [];
                        result['label'] = response.project_manager.label;
                        result['value'] = response.project_manager.value.toString();

                        $('input#edit-project-manager').tokenfield('setTokens', [result]);
                    }

                    var tokenDataEngine4 = new Bloodhound({
                        remote: {
                            url: "/getDevelopers?query=%QUERY",
                            wildcard: "%QUERY",
                            filter: function (response) {
                                return response.devs;
                            }
                        },
                        datumTokenizer: function (d) {
//            console.log(d);
                            return Bloodhound.tokenizers.whitespace(d.label);
                        },
                        queryTokenizer: Bloodhound.tokenizers.whitespace
                    });

                    tokenDataEngine4.initialize();
                    var getResourcesEditRequest = false;
                    $('input#edit-resources').tokenfield({
                        typeahead: [null, {
                                name: "edit-resources",
                                templates: {
                                    suggestion: function (data) {
                                        return '<div>' + data.label + '</div>';
                                    }
                                },
                                /*source: tokenDataEngine4.ttAdapter(),*/
                                source: function (request, response) {

                                    if (getResourcesEditRequest) {
                                        getResourcesEditRequest.abort();
                                    }
                                    getResourcesEditRequest = $.get("/getDevelopers", {
                                        query: request,
                                        selected: $('input#edit-resources').val()
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
                        var existingTokens = $(this).tokenfield('getTokens');
                        $.each(existingTokens, function (index, token) {
                            if (token.value === e.attrs.value)
                                e.preventDefault();
                        });
                        var value = parseInt(e.attrs.value);
                        if (isNaN(value))
                            e.preventDefault();

                    });

                    $('input#edit-resources').tokenfield('setTokens', response.developers);

                    if (response.note != null) {
                        $('#editProjectFrm #edit-project-note').val(response.note.value);
                    } else {
                        $('#editProjectFrm #edit-project-note').val('');
                    }

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

                    $('input#edit-project-skills').tokenfield({
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

                    $('input#edit-project-skills').tokenfield('setTokens', response.skills);

                } else {
                    alert(response.message);
                }
            }
        });

    });

    $('body').on('submit', '#editProjectFrm', function (e) {
        e.preventDefault();

        var submitBtn = $('#edit-project-btn');

        var thisForm = $(this);

        setTimeout(function () {
            submitBtn.val('Please wait..').prop( "disabled", true );
        }, 100);

        var errBag = [];
        if ($('#edit-project-title').val().replace(/ /g, "") == '') {
            errBag.push('Project title Should not be empty');
        }
        if ($('#edit-project-code').val().replace(/ /g, "") == '') {
            errBag.push('Project code Should not be empty');
        }
        if ($('#edit-project-manager').val().replace(/ /g, "") == '') {
            errBag.push('Please Select a project manager');
        }
        //if ($('#edit-resources').val().replace(/ /g, "") == '') {
        //    errBag.push('Please Select project resources');
        //}

        var start = $('#edit-start-date').val();
        var end = $('#edit-end-date').val();
        if(start==''){
            errBag.push('Please Select project start date');
        }
        if(end==''){
            errBag.push('Please Select project end date');
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
                selector: '#editProjectFrm .modal-footer .submit'
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
                                    $('#edit-resource-confirm').val("1");
                                    thisForm.submit();
                                }
                            }
                        });

                    }else if(response.status=='success'){
                        $('#editProject').modal('hide');
                        resetProjectCreateForm();
                        $.notify({
                            message: 'Project Successfully Created'
                        }, {type: 'success'});
                        redirectTo(response.redirect_url);
                    }else if(response.status=='info'){
                        $('#editProject').modal('hide');
                        resetProjectCreateForm();
                        $.notify({
                            message: response.message
                        }, {type: 'info'});

                        redirectTo(response.redirect_url);
                    }else if(response.status=='error'){
                            iddMessage({
                                message: response.message,
                                status: 'danger',
                                selector: '#editProject .modal-footer .submit'
                            });

                    }
                }
            });
        }


    });

    /*$('body').on('change', '#editProjectFrm input', function (e) {
        var hasError = false;
        if ($(this).val() != '') {
            if ($(this).attr('id') != 'edit-start-date' && $(this).attr('id') != 'edit-end-date') {
                $(this).parent().removeClass('has-error');
            } else {
                $(this).parent().parent().removeClass('has-error');
            }
        }

    });*/

    $('#editProject').on('hidden.bs.modal', function () {
        resetEditProjectForm();
    });
});

function resetEditProjectForm() {
    $('#editProjectFrm #edit-project-title').val('');
    $('#editProjectFrm #edit-project-code').val('');
    $('#editProjectFrm #edit-start-date').val('');
    $('#editProjectFrm #edit-end-date').val('');
    $('#editProjectFrm #edit-project-manager').val('');
    $('#editProjectFrm #edit-project-tags').val('');
    $('#editProjectFrm #edit-resources').val('');
    $('#editProjectFrm #edit-project-skills').val('');
    $('#editProjectFrm #edit-project-note').val('');
    $('#edit-start-date').datepicker('destroy');
    $('#edit-end-date').datepicker('destroy');
    
}