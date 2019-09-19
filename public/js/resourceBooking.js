function openBookingWindow(user_id, user_name, project_id, project_name, start, end) {
    $("#addbookingFrm #booking_project_id").val(project_id);
    $("#newBooking span#booking-project-tilte").text(project_name);
    $("#addbookingFrm #booking-start-date").val(start);
    $("#addbookingFrm #booking-end-date").val(end);
    $('#booking-modal-opener').click();
}

$('#newBooking').on('shown.bs.modal', function () {
});



$(document).ready(function () {
    
    $('body').on('focus','#addbookingFrm textarea',function(){
        $(this).animate({ height: "200px" }, 500);
    });
    $('body').on('blur','#addbookingFrm textarea',function(){
        $(this).animate({ height: "100px" }, 500);
    });
    
    $('body').on('change','#booking_allocation_value',function(){
        if($(this).val() != 'custom') {
            $('#booking_allocation_value_row.custom-active').removeClass('custom-active');            
        }else{            
            $('#booking_allocation_value_row').addClass('custom-active');
        }
    });


    $(document).on("submit","#addbookingFrm", function (e) {
        e.preventDefault();

        var submitBtn = $('#add-booking-btn');

        setTimeout(function(){
            submitBtn.prop('disabled', true).html('Please wait..');
        },100);

        var hasError = false;
        var err = [];

        var start = $('#booking-start-date').val();
        var end = $('#booking-end-date').val();
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

        var allocationValue = $('#booking_allocation_custom_value').val();
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

        if(err.length>0){
            setTimeout(function() {
                submitBtn.prop('disabled', false).html(submitBtn.attr('placeholder'));
            },100);
            var errText = '';
            $.each(err, function(index ,value ) {
                var text = (index>0)?'<br/>':'';
                errText+=text+value;
            });

            iddMessage({
                message:errText,
                status:'danger',
                selector:'#newBooking .modal-footer .submit'
                
            });
            err = [];

            return false;
        } else {
            var url = $(this).attr('action').replace('projectId',$('#booking_project_id').val());
            var data = $(this).serialize();
            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                dataType: 'json',
                cache: false,
                success: function (response) {
                    setTimeout(function(){
                        submitBtn.prop('disabled', false).html(submitBtn.attr('placeholder'));
                    },100);

                    if (response.status == 'success') {
                        $('#add-booking-modal-close').click();
                        $('#calendar').fullCalendar('refetchEvents');
                        resetBookingForm();
                        $.notify({
                            message: 'Successfully Allocated'
                        },{
                            type: 'success'
                        });
                    } else {
                        iddMessage({
                            message: response.message,
                            status: 'danger',
                            selector:'#newBooking .modal-footer .submit'
                        });
                    }
                }
            });
        }
    });

    $('body').on('change', '#addbookingFrm input', function (e) {
        var hasError = false;
        if ($(this).val() != '') {
            if ($(this).attr('id') != 'booking-start-date' && $(this).attr('id') != 'booking-end-date' && $(this).attr('id') != 'booking_allocation_value' && $(this).attr('id') != 'booking_allocation_custom_value') {
                $(this).parent().removeClass('has-error');
            } else {
                $(this).parent().parent().removeClass('has-error');
            }
        }
    });

});

$('#newBooking').on('hidden.bs.modal', function () {
    resetBookingForm();
});


$(document).ready(function(){
    $('body').on('change','#booking_allocation_value',function(){
        var value = $(this).val();
        if(value=='custom') {
            $('#booking_allocation_custom_value').val('');
            $('#addbookingFrm .allo-select-wrapper').addClass('custom-active');
        }else{
            $('#booking_allocation_custom_value').val(value);
            $('#addbookingFrm .allo-select-wrapper.custom-active').removeClass('custom-active');

        }
    });
    $('body').on('click', '#addbookingFrm input[data-date-format]', function () {
        $(this).siblings().click();
    });
    $('#booking-start-date-picker').datepicker({
        container: "#booking-project-date-selector",
        daysOfWeekDisabled: [0, 6],
        autoclose: true
    }).on('changeDate', function (e) {
        var selectedDate = moment(e.date).format('YYYY-MM-DD');
        $('#booking-start-date').val(selectedDate);
    });
    $('#booking-end-date-picker').datepicker({
        container: "#booking-project-date-selector",
        autoclose: true,
        daysOfWeekDisabled: [0, 6]
    }).on('changeDate', function (e) {
        var selectedDate = moment(e.date).format('YYYY-MM-DD');
        $('#booking-end-date').val(selectedDate);
    });
});


function resetBookingForm() {
    $('#booking-error-alert').hide();
    $('#booking-error-message').text('');
    $('#booking_allocation_value').val('');
    $('#booking_allocation_custom_value').val('');
    $('#booking_project_id').val('');
    $('#booking-start-date').val('');
    $('#booking-end-date').val('');
    $('#booking_note').val('');
    $('#newBooking div').removeClass('has-error');
    $('#booking_allocation_value_row').removeClass('custom-active');
    $('.rss-alert').remove();
}
                        