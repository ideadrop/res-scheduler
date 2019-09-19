<!-- Modal -->
<div id="newBooking" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form id="addbookingFrm" method="POST" action="{{route('project.allocate.resources',['projectId'=>'projectId'])}}" autocomplete="off">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Schedule booking on project <span id="booking-project-tilte"></span></h4>
                </div>
                <div class="modal-body">
                        <input type="hidden" id="booking_project_id" value="">
                        <input type="hidden" name="resource_id" id="booking_resource_id" value="{{$user->id}}">
                        <div style="display:none" class="alert alert-danger alert-dismissible" id="booking-error-alert" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <span id="booking-error-message">This is an error message</span>
                        </div>
                        <div class="form-group row" id="booking-project-date-selector">
                            <div class="col-md-6">
                                <label>* Start Date:</label>
                                <div class="input-group date">
                                    <input id="booking-start-date" name="start_date" data-date-format="yyyy-mm-dd" type="text" readonly="readonly" class="form-control"/>
                                    <span class="input-group-addon" id="booking-start-date-picker">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>* End Date:</label>
                                <div class="input-group date">
                                    <input id="booking-end-date" name="end_date" data-date-format="yyyy-mm-dd" type="text" readonly="readonly" class="form-control"/>
                                    <span class="input-group-addon" id="booking-end-date-picker">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <style>
                            .allo-select-wrapper .allo-value-container{
                                display: none;
                            }
                            .allo-select-wrapper.custom-active .allo-value-container{
                                display: block;
                            }
                            .allo-select-wrapper.custom-active .allo-selector-container{
                                width: 50% !important;
                            }
                            .allo-select-wrapper .allo-selector-container{
                                width: 100% !important;
                            }
                        </style>
                        <div class="form-group row allo-select-wrapper" id="booking_allocation_value_row">
                            <div class="col-md-12"><label>* Allocation %:</label></div>
                            <div class="col-md-6 allo-selector-container">
                                <select id="booking_allocation_value" class="form-control">
                                    <option value="">Select % of booking per day</option>
                                    <option value="25">25%</option>
                                    <option value="50">50%</option>
                                    <option value="75">75%</option>
                                    <option value="100">100%</option>
                                    <option value="custom">Custom</option>
                                </select>
                            </div>
                            <div class="col-md-6 allo-value-container">
                                <input type="text" id="booking_allocation_custom_value" value="" name="allocation" class="form-control" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Booking Notes:</label><i class="bar"></i>
                            <textarea class="form-control" id="booking_note" name="allocation_note" placeholder="Enter notes about the booking here"></textarea>
                        </div>

                </div>
                <div class="modal-footer">
                    <div class="submit">
                        <button type="submit" id="add-booking-btn" class="btn btn-primary" placeholder="Add Booking">Add Booking</button>
                        <button type="button" id="add-booking-modal-close" class="btn btn-gry" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

