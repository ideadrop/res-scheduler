function resetAllocationWindow() {
    $('#project-resource-booking-modal input').val('');
    $('#project-resource-booking-modal textarea').val('');
    $('#project-resource-booking-modal select').prop('selectedIndex', 0);
    $('.allo-select-wrapper.custom-active').removeClass('custom-active');
    $('.rss-alert').remove();
}
function openAllocationWindow(start, end, resourceId) {
    resetAllocationWindow();
    $('#project-booking-start').val(start);
    $('#project-booking-end').val(end);
    if (resourceId !== undefined) {
        $('#resource-selector').val(resourceId);
    }
    $('#project-resource-booking-modal').modal('show');
}
function resetEditAllocationWindow() {
    $('#project-resource-edit-modal input').val('');
    $('#project-resource-edit-modal textarea').val('');
    $('#project-resource-edit-modal select').prop('selectedIndex', 0);
    $('.allo-edit-select-wrapper.custom-active').removeClass('custom-active');
    $('.rss-alert').remove();
}
var descriptionRequest = false;
function fillAllocationEditDescription(allocationId) {

    if (descriptionRequest && descriptionRequest.readyState != 4) {
        descriptionRequest.abort();
    }

    descriptionRequest = $.ajax({
        url: '/project/allocation/' + allocationId + '/description',
        method: "POST",
        success: function (response) {
            if (response.status == 'success') {
                $('#allocation-edit-note').val(response.data);
            }
        }
    });
}
function openEditAllocationWindow(calEvent) {
    console.log();
    resetEditAllocationWindow();
    fillAllocationEditDescription(calEvent.id);
    $('#project-booking-edit-start').val(calEvent.start.format('YYYY-MM-DD'));
    $('#project-booking-edit-end').val(calEvent.end.format('YYYY-MM-DD'));

    $('#resource-edit-selector').val(calEvent.resourceId);
    $('#edit-allocation-resource-id').val(calEvent.resourceId);
    $('#allocation-edit-id').val(calEvent.id);

    var percentage = calEvent.allocationValue;
    $('#project-resource-allocation-edit').val(percentage);
    if ([25, 50, 75, 100].indexOf(parseInt(percentage)) == -1) {
        $('.allo-edit-select-wrapper').addClass('custom-active');
        $('#allocation-edit-selector').val('custom');
    } else {
        $('#allocation-edit-selector').val(percentage);
    }

    $('.allocation-edit-username').html(calEvent.title);

    $('#project-resource-edit-modal').modal('show');
}

$(document).ready(function () {

    /*################## RESOURCE ALLOCATION SCRIPTS STARTS ###################*/

    $('#project-booking-start-picker').datepicker({
        container: "#booking-project-date-selector",
        daysOfWeekDisabled: [0, 6],
        autoclose: true
    }).on('changeDate', function (e) {
        var selectedDate = moment(e.date).format('YYYY-MM-DD');
        $('#project-booking-start').val(selectedDate);
    });
    $('#project-booking-end-picker').datepicker({
        container: "#booking-project-date-selector",
        autoclose: true,
        daysOfWeekDisabled: [0, 6]
    }).on('changeDate', function (e) {
        var selectedDate = moment(e.date).format('YYYY-MM-DD');
        $('#project-booking-end').val(selectedDate);
    });

    $('body').on('click', '.project-resource-form input[data-date-format]', function () {
        $(this).siblings().click();
    });
    $('body').on('focus', '#project-booking-form textarea', function () {
        $(this).animate({height: "200px"}, 500);
    });
    $('body').on('blur', '#project-booking-form textarea', function () {
        $(this).animate({height: "100px"}, 500);
    });
    $('body').on('change', '#allocation-selector', function () {
        var value = $(this).val();

        if (value != 'custom') {
            $('.allo-select-wrapper.custom-active').removeClass('custom-active');
            $('#project-resource-allocation').val(value);
        } else {
            $('#project-resource-allocation').val('');
            $('.allo-select-wrapper').addClass('custom-active');
        }
    });
    $('body').on('submit', 'form#project-booking-form', function (e) {
        e.preventDefault();
        var thisForm = $(this);
        var submitBtn = $('#allocate-submit');
        $('.rss-alert').remove();

        setTimeout(function () {
            submitBtn.prop('disabled', true).html('Please wait...');
        }, 100);


        var err = [];

        var start = $('#project-booking-start').val();
        var end = $('#project-booking-end').val();
        if (start == '') {
            err.push('Start Date is missing');
        }
        if (end == '') {
            err.push('End Date is missing');
        }
        if ((start != '') && (end != '')) {
            if (new Date(start) >= new Date(end)) {
                err.push('Please check start and end dates');
            }
        }

        if ($('#resource-selector').val() == '') {
            err.push('Select a resource');
        }

        var allocationValue = $('#project-resource-allocation').val();
        if (isNaN(allocationValue)) {
            err.push('Enter a valid percentage between 1 to 100');
        } else {
            if (allocationValue == '') {
                err.push('Select day % allocation');
            } else if (isNaN(allocationValue)) {
                err.push('Enter a valid number');
            } else if (allocationValue <= 0 || allocationValue > 100) {
                err.push('Enter a valid percentage between 1 to 100');
            }
        }

        if (err.length > 0) {
            setTimeout(function () {
                submitBtn.prop('disabled', false).html(submitBtn.attr('placeholder'));
            }, 100);
            var errText = '';
            $.each(err, function (index, value) {
                var text = (index > 0) ? '<br/>' : '';
                errText += text + value;
            });

            iddMessage({
                message: errText,
                status: 'danger',
                selector: '#project-resource-booking-modal .modal-footer .submit'
            });
            return false;
        } else {
            $.ajax({
                url: thisForm.attr('action'),
                data: thisForm.serialize(),
                method: thisForm.attr('method'),
                success: function (response) {
                    setTimeout(function () {
                        submitBtn.prop('disabled', false).html(submitBtn.attr('placeholder'));
                    }, 100);
                    if (response.status == 'success') {
                        $('#project-calendar').fullCalendar('refetchEvents');
                        $('#project-resource-booking-modal').modal('hide');
                        $.notify({
                            message: 'Successfully Allocated'
                        }, {
                            type: 'success'
                        });
                    } else if (response.status == 'error') {
                        iddMessage({
                            message: response.message,
                            status: 'danger',
                            selector: '#project-resource-booking-modal .modal-footer .submit'
                        });
                    }
                }
            });
        }
    });
    /*################## RESOURCE ALLOCATION SCRIPTS ENDS ###################*/

    /*################## RESOURCE ALLOCATION EDIT SCRIPTS STARTS ############*/

    $('#project-booking-edit-start-picker').datepicker({
        container: "#booking-project-date-edit-selector",
        daysOfWeekDisabled: [0, 6],
        autoclose: true
    }).on('changeDate', function (e) {
        var selectedDate = moment(e.date).format('YYYY-MM-DD');
        $('#project-booking-edit-start').val(selectedDate);
    });
    $('#project-booking-edit-end-picker').datepicker({
        container: "#booking-project-date-edit-selector",
        autoclose: true,
        daysOfWeekDisabled: [0, 6]
    }).on('changeDate', function (e) {
        var selectedDate = moment(e.date).format('YYYY-MM-DD');
        $('#project-booking-edit-end').val(selectedDate);
    });

    $('body').on('change', '#allocation-edit-selector', function () {
        var value = $(this).val();

        if (value != 'custom') {
            $('.allo-edit-select-wrapper.custom-active').removeClass('custom-active');
            $('#project-resource-allocation-edit').val(value);
        } else {
            $('#project-resource-allocation-edit').val('');
            $('.allo-edit-select-wrapper').addClass('custom-active');
        }
    });
    $('body').on('focus', '#project-booking-edit-form textarea', function () {
        $(this).animate({height: "200px"}, 500);
    });
    $('body').on('blur', '#project-booking-edit-form textarea', function () {
        $(this).animate({height: "100px"}, 500);
    });
    $('body').on('submit', 'form#project-booking-edit-form', function (e) {
        e.preventDefault();
        var thisForm = $(this);
        var submitBtn = $('#allocate-edit-submit');

        setTimeout(function () {
            submitBtn.prop('disabled', true).html('Please wait..');
        }, 100);
        $('.rss-alert').remove();

        var err = [];

        var start = $('#project-booking-edit-start').val();
        var end = $('#project-booking-edit-end').val();
        if (start == '') {
            err.push('Start Date is missing');
        }
        if (end == '') {
            err.push('End Date is missing');
        }
        if ((start != '') && (end != '')) {
            if (new Date(start) >= new Date(end)) {
                err.push('Please check start and end dates');
            }
        }

        if ($('#resource-edit-selector').val() == '') {
            err.push('Select a resource');
        }
        var allocationValue = $('#project-resource-allocation-edit').val();
        if (isNaN(allocationValue)) {
            err.push('Enter a valid percentage between 1 to 100');
        } else {
            var allocationValue = parseInt($('#project-resource-allocation-edit').val());
            if (allocationValue == '') {
                err.push('Select day % allocation');
            } else if (isNaN(allocationValue)) {
                err.push('Enter a valid number');
            } else if (allocationValue <= 0 || allocationValue > 100) {
                err.push('Enter a valid percentage between 1 to 100');
            }
        }

        if (err.length > 0) {
            setTimeout(function () {
                submitBtn.prop('disabled', false).html(submitBtn.attr('placeholder'));
            }, 100);
            var errText = '';
            $.each(err, function (index, value) {
                var text = (index > 0) ? '<br/>' : '';
                errText += text + value;
            });

            iddMessage({
                message: errText,
                status: 'danger',
                selector: '#project-resource-edit-modal .modal-footer .submit'
            });
            return false;
        } else {
            $.ajax({
                url: thisForm.attr('action'),
                data: thisForm.serialize(),
                method: thisForm.attr('method'),
                success: function (response) {
                    setTimeout(function () {
                        submitBtn.prop('disabled', false).html(submitBtn.attr('placeholder'));
                    }, 100);
                    if (response.status == 'success') {
                        $('#project-calendar').fullCalendar('refetchEvents');
                        $('#project-resource-edit-modal').modal('hide');
                        $.notify({
                            message: 'Allocation Successfully Updated'
                        }, {type: 'success'});
                    } else if (response.status == 'error') {
                        iddMessage({
                            message: response.message,
                            status: 'danger',
                            selector: '#project-resource-edit-modal .modal-footer .submit'
                        });
                    }
                }
            });
        }
    });
    $('body').on('click', '#delete-allocation', function () {
        var deleteBtn = $(this);
        bootbox.confirm({
            message: "Are you sure you want to delete this allocation?",
            callback: function (result) {
                /* result is a boolean; true = OK, false = Cancel*/
                deleteBtn.prop('disabled', true).html('Please wait...');

                var allocationId = $('#allocation-edit-id').val();
                if (result == true && allocationId != '') {
                    $.ajax({
                        url: $('#delete-allocation').attr('btn-action'),
                        data: {
                            allocation_id: allocationId
                        },
                        method: 'POST',
                        success: function (response) {
                            setTimeout(function () {
                                deleteBtn.prop('disabled', false).html(deleteBtn.attr('placeholder'));
                            }, 100);
                            if (response.status == 'success') {
                                $('#project-calendar').fullCalendar('refetchEvents');
                                $('#project-resource-edit-modal').modal('hide');
                                $.notify({
                                    message: 'Allocation Successfully Deleted'
                                }, {type: 'success'});

                            } else if (response.status == 'error') {
                                iddMessage({
                                    message: response.message,
                                    status: 'danger',
                                    selector: '#project-resource-edit-modal .modal-footer .submit'
                                });
                            }
                        }
                    });
                }
                if (result == false){
                  deleteBtn.prop('disabled', false).html(deleteBtn.attr('placeholder'));
                }

            }
        })
    });
    /*################## RESOURCE ALLOCATION EDIT SCRIPTS STARTS ###################*/

});
