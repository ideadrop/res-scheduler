<!-- Modal -->
<div id="editProject" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form id="editProjectFrm" method="POST" action="{{ route('project.update') }}">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit Project Details</h4>
            </div>
            <div class="modal-body">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <input type="hidden" name="project_id" id="edit_project_id" value=""/>                    
                    <div class="form-group">
                        <label for="project-title">Project Title <span class="field-req">*</span></label><i class="bar"></i>
                        <input class="form-control" type="text" id="edit-project-title" value="" name="edit-project-title" placeholder="Enter project title"/>
                    </div>
                    <div class="form-group">
                        <label for="project-code">Project Code <span class="field-req">*</span></label><i class="bar"></i>
                        <input class="form-control" type="text" id="edit-project-code" value="" name="edit-project-code" placeholder="Enter project code"/>
                    </div>
                    <div class="form-group">
                        <label for="project-manager">Project Manager <span class="field-req">*</span></label><i class="bar"></i>
                        <input class="tt-input form-control" type="text" id="edit-project-manager" name="edit-project-manager" class="typeahead tm-input form-control tm-input-info" placeholder="Search and select a project manager" />
                    </div>
                    <div class="form-group">
                        <label for="resources">Add Resources</label><i class="bar"></i>
                        <input class="form-control" type="text" id="edit-resources" name="edit-resources" class="typeahead tm-input form-control tm-input-info" placeholder="Search and select resources"/>
                    </div>
                    <div class="form-group row" id="project-date-selector">
                        <div class="col-md-6">
                            <label>Start Date <span class="field-req">*</span></label>
                            <input type="text" data-date-format="yyyy-mm-dd" id="edit-start-date" name="edit-start-date" readonly="readonly" class="form-control popup-datepicker" placeholder="Click to select"/>
                        </div>
                        <div class="col-md-6">
                            <label>End Date <span class="field-req">*</span></label>
                            <input type="text" id="edit-end-date" data-date-format="yyyy-mm-dd" name="edit-end-date" readonly="readonly" class="form-control popup-datepicker" placeholder="Click to select"/>
                        </div>
                    </div>   
                    <div class="form-group">
                        <label>Project Notes</label><i class="bar"></i>
                        <textarea class="form-control" id="edit-project-note" name="edit-project-note" placeholder="Enter notes about the project here"></textarea>                  
                    </div>
                    <div class="form-group">
                        <label for="project-tags">Tags</label><i class="bar"></i>
                        <input type="text" id="edit-project-tags" name="edit-project-tags" class="typeahead tm-input form-control tm-input-info" placeholder="Type and press enter to add tags">
                    </div>
                    <div class="form-group">
                        <label for="edit-project-skills">Add/Remove Skills Needed</label><i class="bar"></i>
                        <input type="text" id="edit-project-skills" name="edit-project-skills" class="typeahead tm-input form-control tm-input-info" placeholder="Type and press enter to add skills">
                    </div>

            </div>
            <div class="modal-footer">
                <div class="submit">
                    <input type="hidden" id="edit-resource-confirm" value="" name="resource_confirm">
                    <button type="submit" id="edit-project-btn" class="btn btn-primary" placeholder="Update">Update</button>
                    <button type="button" class="btn btn-gry" data-dismiss="modal">Close</button>
                </div>
            </div>
            </form>
        </div>

    </div>
</div>

