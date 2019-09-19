function openEditBookingWindow(edit_booking_id, project_name) {
    $("#editBookingFrm #edit_booking_id").val(edit_booking_id);

    $('#edit-booking-modal-opener').click();
}

$('#editBooking').on('shown.bs.modal', function () {
    $('.rss-alert').remove();
    $.ajax({
        url: "/resources/getbooking/" + $('#edit_booking_id').val(),
        type: 'GET',
        async:false,
        dataType: 'json',
        success: function (response) {
            if (response.status == 'success') {


                var percentage = response.data.allocation_value;
                $('#edit_booking_allocation_custom_value').val(percentage);
                if([25,50,75,100].indexOf(parseInt(percentage))==-1){
                    $('#edit_booking_allocation_value_row.allo-select-wrapper').addClass('custom-active');
                    $('#edit_booking_allocation_value').val('custom');
                }else{
                    $('#edit_booking_allocation_value').val(percentage);
                }

                $('#edit_booking_project_id').val(response.data.project_id);
                $('#edit-booking-start-date').val(response.data.start_date);
                $('#edit-booking-end-date').val(response.data.end_date);
                $('#edit_booking_note').val(response.data.note);
                $('#edit-booking-project-tilte').text(response.data.project_name);
                /*var startDatePicker = $('#edit-booking-start-date').datepicker({
                    container: "#editBooking .modal-body",
                    autoclose: true,
                    daysOfWeekDisabled: "0,6"
                }).on('changeDate', function (e) {
                    $('#edit-booking-end-date').datepicker('clearDates');
                    $('#edit-booking-end-date').datepicker('setStartDate', $('#edit-booking-start-date').datepicker('getDate'));
                });*/



               /* var endDatePicker = $('#edit-booking-end-date').datepicker({
                    container: "#editBooking .modal-body",
                    autoclose: true,
                    daysOfWeekDisabled: "0,6"
                });*/

            } else {
                $('#booking-error-alert').show();
                $('#booking-error-message').text(response.message);

            }
        }
    });

});

$(document).ready(function () {
    $('body').on('click', '#editBookingFrm input[data-date-format]', function () {
        $(this).siblings().click();
    });
    $('#edit-booking-start-date-picker').datepicker({
        container: "#edit_booking-project-date-selector",
        daysOfWeekDisabled: [0, 6],
        autoclose: true
    }).on('changeDate', function (e) {
        var selectedDate = moment(e.date).format('YYYY-MM-DD');
        $('#edit-booking-start-date').val(selectedDate);
    });
    $('#edit-booking-end-date-picker').datepicker({
        container: "#edit_booking-project-date-selector",
        autoclose: true,
        daysOfWeekDisabled: [0, 6]
    }).on('changeDate', function (e) {
        var selectedDate = moment(e.date).format('YYYY-MM-DD');
        $('#edit-booking-end-date').val(selectedDate);
    });

    $(document).on("change", "#edit_booking_allocation_value", function () {
        if ($(this).val() == 'custom') {
            $('#edit_booking_allocation_value_row').addClass('custom-active');
            $('#edit_booking_allocation_custom_value').val('');
        } else {
            $('#edit_booking_allocation_value_row.custom-active').removeClass('custom-active');
        }
    });

    $('body').on('focus', '#editBookingFrm textarea', function () {
        $(this).animate({height: "200px"}, 500);
    });
    $('body').on('blur', '#editBookingFrm textarea', function () {
        $(this).animate({height: "100px"}, 500);
    });

    $('body').on("click", "#delete-booking-btn", function (e) {
        var deleteBtn = $(this);
        bootbox.confirm({
            message: "Are you sure you want to delete this allocation?",
            callback: function (result) {
                deleteBtn.prop('disabled', true).html('Please wait...');
                var allocationId = $('#edit_booking_id').val();
                if(result == true && allocationId!='') {
                    $.ajax({
                        url: "/resources/deletebooking/" + allocationId,
                        type: 'GET',
                        dataType: 'json',
                        success: function (response) {
                            setTimeout(function() {
                                deleteBtn.prop('disabled', false).html(deleteBtn.attr('placeholder'));
                            },100);
                            if (response.status == 'success') {
                                $('#edit-booking-modal-close').click();
                                $('#calendar').fullCalendar('refetchEvents');
                                resetEditBookingForm();
                                $.notify({
                                    message: 'Successfully removed'
                                }, {
                                    type: 'success'
                                });
                            } else {
                                $('#booking-error-alert').show();
                                $('#booking-error-message').text(response.message);
                            }
                        }
                    });
                }
                if (result == false){
                  deleteBtn.prop('disabled', false).html(deleteBtn.attr('placeholder'));
                }
            }
        });
    });


    $(document).on("submit", "#editBookingFrm", function (e) {
        e.preventDefault();

        var submitBtn = $('#edit-booking-btn');

        setTimeout(function(){
            submitBtn.prop('disabled', true).html('Please wait...');
        },100);

        var err = [];
        var start = $('#edit-booking-start-date').val();
        var end = $('#edit-booking-end-date').val();
        if(start==''){
            err.push('Start Date is missing');
        }
        if(end==''){
            err.push('End Date is missing');
        }
        if( (start!='') && (end!='')) {
            if(new Date(start) >= new Date(end)){
                err.push('Please check start and end dates');
            }
        }

        var allocationValue = $('#edit_booking_allocation_custom_value').val();
        if(isNaN(allocationValue)) {
            err.push('Enter a valid percentage between 1 to 100');
        }else{
            if(allocationValue==''){
                err.push('Select day % allocation');
            }else if(isNaN(allocationValue)){
                err.push('Enter a valid number');
            }else if(allocationValue<=0 || allocationValue>100){
                err.push('Enter a valid percentage between 1 to 100');
            }
        }
        if (err.length > 0) {
            setTimeout(function() {
                submitBtn.prop('disabled', false).html(submitBtn.attr('placeholder'));
            },100);
            var errText = '';
            $.each(err, function (index, value) {
                var text = (index > 0) ? '<br/>' : '';
                errText += text + value;
            });

            iddMessage({
                message: errText,
                status: 'danger',
                selector: '#editBooking .modal-footer .submit'

            });
            err = [];
            return false;
        } else {

            var url = $(this).attr('action').replace('projectId',$('#edit_booking_project_id').val());
            var data = $(this).serialize();
            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                dataType: 'json',
                cache: false,
                success: function (response) {
                    setTimeout(function() {
                        submitBtn.prop('disabled', false).html(submitBtn.attr('placeholder'));
                    },100);
                    if (response.status == 'success') {
                        $('#edit-booking-modal-close').click();
                        $('#calendar').fullCalendar('refetchEvents');
                        resetEditBookingForm();
                        $.notify({
                            message: 'Successfully updated'
                        }, {
                            type: 'success'
                        });
                    } else {
                        iddMessage({
                            message: response.message,
                            status: 'danger',
                            selector: '#editBooking .modal-footer .submit'

                        });
                        return false;
                    }
                }
            });
        }
    });

    $('body').on('change', '#editBookingFrm input', function (e) {
        var hasError = false;
        if ($(this).val() != '') {
            if ($(this).attr('id') != 'edit-booking-start-date' && $(this).attr('id') != 'edit-booking-end-date' && $(this).attr('id') != 'edit_booking_allocation_value' && $(this).attr('id') != 'edit_booking_allocation_custom_value') {
                $(this).parent().removeClass('has-error');
            } else {
                $(this).parent().parent().removeClass('has-error');
            }
        }
    });
    $('body').on('change','#edit_booking_allocation_value',function(){
        var value = $(this).val();
        if(value=='custom') {
            $('#edit_booking_allocation_custom_value').val('');
            $('#editBookingFrm .allo-select-wrapper').addClass('custom-active');
        }else{
            $('#edit_booking_allocation_custom_value').val(value);
            $('#editBookingFrm .allo-select-wrapper.custom-active').removeClass('custom-active');

        }
    });
    $('#newBooking').on('hidden.bs.modal', function () {
        resetEditBookingForm();
    });
});

function resetEditBookingForm() {
    $('#edit-booking-error-alert').hide();
    $('#edit-booking-error-message').text('');
    $('#edit_booking_allocation_value').val('');
    $('#edit_booking_allocation_custom_value').val('');
    $('#edit_booking_project_id').val('');
    $('#edit-booking-start-date').val('');
    $('#edit-booking-end-date').val('');
    $('#edit_booking_note').val('');
    $('#editBookingFrm div').removeClass('has-error');
    $('#editBookingFrm .alert .close').click();
    $('#edit_booking_allocation_value_row').removeClass('custom-active');
    $('#editBookingFrm .rss-alert').remove();
}
