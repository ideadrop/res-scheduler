<div class="row">
    <div class="col-lg-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-long-arrow-right fa-fw"></i> Project Skills</h3>
            </div>
            <div class="panel-body">
                <div id="donut-chart-project"></div>
                @if($authUser->can(['view-report']))
                    <div class="text-right">
                        <a href="{{ route('reports.skill.based') }}">View Report <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-long-arrow-right fa-fw"></i> User Skills</h3>
            </div>
            <div class="panel-body">
                <div id="donut-chart-user"></div>
                @if($authUser->can(['view-report']))
                    <div class="text-right">
                        <a href="{{ route('reports.skill.based') }}">View Report <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>