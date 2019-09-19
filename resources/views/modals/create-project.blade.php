<!-- Modal -->
<div id="newProject" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <form id="addProjectFrm" method="POST" action="{{ route('project.store') }}">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Create A New Project</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">

                    <div class="form-group">
                        <label for="project-title">Project Title <span class="field-req">*</span></label><i
                                class="bar"></i>
                        <input class="form-control" type="text" id="project-title" name="project-title"
                               placeholder="Enter project title"/>
                    </div>
                    <div class="form-group">
                        <label for="project-code">Project Code <span class="field-req">*</span></label><i
                                class="bar"></i>
                        <input class="form-control" type="text" id="project-code" name="project-code"
                               placeholder="Enter project code"/>
                    </div>
                    <div class="form-group">
                        <label for="project-manager">Project Manager <span class="field-req">*</span></label><i
                                class="bar"></i>
                        <input class="tt-input form-control" type="text" id="project-manager" name="project-manager"
                               class="typeahead tm-input form-control tm-input-info"
                               placeholder="Search and select one project manager"/>
                    </div>
                    <div class="form-group">
                        <label for="resources">Add Resources</label><i class="bar"></i>
                        <input class="form-control" type="text" id="resources" name="resources"
                               class="typeahead tm-input form-control tm-input-info"
                               placeholder="Search and select resources"/>
                    </div>
                    <div class="form-group row" id="project-date-selector">
                        <div class="col-md-6">
                            <label>Start Date <span class="field-req">*</span></label>
                            <input type="text" data-date-format="yyyy-mm-dd" id="start-date" name="start-date"
                                   readonly="readonly" class="form-control popup-datepicker"
                                   placeholder="Click to select"/>
                        </div>
                        <div class="col-md-6">
                            <label>End Date <span class="field-req">*</span></label>
                            <input type="text" id="end-date" data-date-format="yyyy-mm-dd" name="end-date"
                                   readonly="readonly" class="form-control popup-datepicker"
                                   placeholder="Click to select"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Project Notes:</label><i class="bar"></i>
                        <textarea class="form-control" name="project-note"
                                  placeholder="Enter notes about the project here"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="project-tags">Add Tags</label><i class="bar"></i>
                        <input type="text" id="project-tags" name="project-tags"
                               class="typeahead tm-input form-control tm-input-info"
                               placeholder="Type and press enter to add tags">
                    </div>
                    <div class="form-group">
                        <label for="project-skills">Add Skills Needed</label><i class="bar"></i>
                        <input type="text" id="project-skills" name="project-skills"
                               class="typeahead tm-input form-control tm-input-info"
                               placeholder="Type and press enter to add skills">
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="submit">
                        <input type="hidden" id="create-resource-confirm" value="" name="resource_confirm">
                        <button type="submit" id="add-project-btn" class="btn btn-primary" placeholder="Add Project">Add Project</button>
                        <button type="button" class="btn btn-gry" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

