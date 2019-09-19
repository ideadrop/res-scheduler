<!-- Modal -->
<div id="project-resource-edit-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <form id="project-booking-edit-form" method="POST" action="{{route('project.allocate.edit.resources',['projectId'=>$project->id])}}" class="project-resource-form">
            <div class="modal-content"><!-- Modal content-->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit allocation : <span class="allocation-edit-username"></span> on project "{{$project->name}}"</h4>
                </div>

                <div class="modal-body">
                    <div class="form-group row" id="booking-project-date-edit-selector">
                        <div class='col-sm-6'>
                            <div class="form-group">
                                <label>* Start Date:</label>
                                <div class='input-group date'>
                                    <input type="text" id="project-booking-edit-start" data-date-format="yyyy-mm-dd" name="start_date" readonly="readonly" class="form-control" required/>
                                    <span class="input-group-addon" id="project-booking-edit-start-picker">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class='col-sm-6'>
                            <div class="form-group">
                                <label>* End Date:</label>
                                <div class='input-group date'>
                                    <input type="text" id="project-booking-edit-end" data-date-format="yyyy-mm-dd" name="end_date" readonly="readonly" class="form-control" required/>
                                    <span class="input-group-addon" id="project-booking-edit-end-picker">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row hidden">
                        <input type="hidden" id="allocation-edit-id" name="allocation_id" value=""/>
                        <input type="hidden" id="edit-allocation-resource-id" name="resource_id" value=""/>
                        <div class="col-md-12">
                            <label>* Resource:</label>
                            <select id="resource-edit-selector" class="form-control" disabled>
                                <option value="">Select Resource</option>
                                @foreach($resources as $resource)
                                <option value="{{$resource->id}}">{{$resource->title}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <style>
                        .allo-edit-select-wrapper .allo-value-container{
                            display: none;
                        }
                        .allo-edit-select-wrapper.custom-active .allo-value-container{
                            display: block;
                        }
                        .allo-edit-select-wrapper.custom-active .allo-selector-container{
                            width: 50% !important;
                        }
                        .allo-edit-select-wrapper .allo-selector-container{
                            width: 100% !important;
                        }
                    </style>
                    <div class="form-group row allo-edit-select-wrapper">
                        <label class="col-md-12">* Allocation %:</label>
                        <div class="col-md-6 allo-selector-container">
                            <select id="allocation-edit-selector"  class="form-control">
                                <option value="">Select % of booking per day</option>
                                <option value="25">25%</option>
                                <option value="50">50%</option>
                                <option value="75">75%</option>
                                <option value="100">100%</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                        <div class="col-md-6 allo-value-container">
                            <input type="text" id="project-resource-allocation-edit" name="allocation" class="form-control" value="" placeholder="Enter % of booking per day (1-100)" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Allocation Notes:</label><i class="bar"></i>
                        <textarea id="allocation-edit-note" class="form-control" name="allocation_note" placeholder="Enter notes about the allocation here"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="submit">
                        <button type="submit" id="allocate-edit-submit" class="btn btn-primary" placeholder="Update Allocation">Update Allocation</button>
                        <button type="button" id="delete-allocation" btn-action="{{route('project.allocate.delete.resources',['projectId'=>$project->id])}}" class="btn btn-danger" placeholder="Delete Allocation">Delete Allocation</button>
                        <button type="button" class="btn btn-gry" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

