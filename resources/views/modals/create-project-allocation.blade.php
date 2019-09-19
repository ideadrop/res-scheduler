<!-- Modal -->
<div id="project-resource-booking-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <form id="project-booking-form" method="POST"
              action="{{route('project.allocate.resources',['projectId'=>$project->id])}}"
              class="project-resource-form">
            <div class="modal-content"><!-- Modal content-->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Schedule booking on project : {{$project->name}}</h4>
                </div>

                <div class="modal-body">
                    <div class="form-group row" id="booking-project-date-selector">
                        <div class='col-sm-6'>
                            <div class="form-group">
                                <label>* Start Date:</label>

                                <div class='input-group date'>
                                    <input type="text" id="project-booking-start" data-date-format="yyyy-mm-dd"
                                           name="start_date" readonly="readonly" class="form-control" required/>
                                            <span class="input-group-addon" id="project-booking-start-picker">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                </div>
                            </div>
                        </div>
                        <div class='col-sm-6'>
                            <div class="form-group">
                                <label>* End Date:</label>

                                <div class='input-group date'>
                                    <input type="text" id="project-booking-end" data-date-format="yyyy-mm-dd"
                                           name="end_date" readonly="readonly" class="form-control" required/>
                                        <span class="input-group-addon" id="project-booking-end-picker">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label>* Resource:</label>
                            <select id="resource-selector" name="resource_id" class="form-control">
                                <option value="">Select Resource</option>
                                @foreach($resources as $resource)
                                    <option value="{{$resource->id}}">{{$resource->title}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <style>
                        .allo-select-wrapper .allo-value-container {
                            display: none;
                        }

                        .allo-select-wrapper.custom-active .allo-value-container {
                            display: block;
                        }

                        .allo-select-wrapper.custom-active .allo-selector-container {
                            width: 50% !important;
                        }

                        .allo-select-wrapper .allo-selector-container {
                            width: 100% !important;
                        }
                    </style>
                    <div class="form-group row allo-select-wrapper">
                        <label class="col-md-12">* Allocation %:</label>

                        <div class="col-md-6 allo-selector-container">
                            <select id="allocation-selector" class="form-control">
                                <option value="">Select % of booking per day</option>
                                <option value="25">25%</option>
                                <option value="50">50%</option>
                                <option value="75">75%</option>
                                <option value="100">100%</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                        <div class="col-md-6 allo-value-container">
                            <input type="text" id="project-resource-allocation" name="allocation" class="form-control"
                                   value="" placeholder="Enter % of booking per day (1-100)"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Allocation Notes:</label><i class="bar"></i>
                        <textarea class="form-control" name="allocation_note"
                                  placeholder="Enter notes about the allocation here"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="submit">
                        <button type="submit" id="allocate-submit" class="btn btn-primary"
                                placeholder="Allocate Resource">Allocate Resource
                        </button>
                        <button type="button" class="btn btn-gry" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

