@extends('layouts.master')
@section('styles')
        <!-- fullcalendar css -->
<link href="{{asset_versioned('/css/fullcalendar.print.css')}}" rel="stylesheet" media="print">
<link href="{{asset_versioned('/css/fullcalendar.css')}}" rel="stylesheet">
<link href="{{asset_versioned('/css/jquery.qtip.css')}}" rel="stylesheet">
<!-- scheduler css -->
<link href="{{asset_versioned('/css/scheduler.css')}}" rel="stylesheet">
<link href="{{asset_versioned('/css/plugins/chosen/chosen.css')}}" rel="stylesheet">
@endsection
@section('content')
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <ol class="breadcrumb">
                    <li>
                        <a>Reports</a>
                    </li>
                    <li class="active">
                        <a>User Based</a>
                    </li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="pull-left">
                    <h2>User Based Report</h2>
                </div>
                <div class="pull-right"></div>
            </div>
        </div>
        <div class="row report-filter-container">
            <div class="pull-left">
                <form id="user-based-report-form" method="POST" action="{{route('reports.user.based.fetch')}}" class="user-based-report-form form-inline" autocomplete="off">
                <div class="form-group">
                    <div class="col-md-12">
                        <input id="report-user-search" name="report_user_search" placeholder="Search name or email" type="text" class="form-control" value="{{$fillSearch}}"/>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <select name="skills[]" id="filter-skills" class="
                        " data-placeholder="Filter user with skill" multiple>
                            @foreach($skills as $skill)
                            <option value="{{$skill->id}}" {{(in_array($skill->id,$fillSkill))?'selected':''}}>{{$skill->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <a id="report-filter-reset" class="btn btn-default" onclick="resetReportFilter();"><span class="glyphicon glyphicon-filter"></span> Reset Filter</a>
                        <a id="report-export" data-href="{{route('reports.user.based.export')}}" class="btn btn-default"><span class="glyphicon glyphicon-download"></span> Export</a>
                    </div>
                </div>
                {{--<input id="filter-start" name="start" value="" type="hidden">
                <input id="filter-end" name="end" value="" type="hidden">--}}
            </form>
            </div>
            <div class="pull-right">
                <div class="col-md-12">
                    <div class="btn-group report-view-switcher">
                        <a data-href="{{route('reports.user.based')}}" class="btn btn-default"><span class="fa fa-list-alt"></span> Detailed view</a>
                        <a data-href="{{route('reports.user.based.calender')}}" class="btn btn-default active"><span class="fa fa-calendar"></span> Calender view</a>
                    </div>
                </div>
            </div>
            <div class="col-md-12"><hr></div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="white-block">
                    <div id='user-report-calendar'></div>
                    <a data-toggle="modal" id="booking-modal-opener" data-target="#newBooking"style="visibility: hidden;"></a>
                    <a data-toggle="modal" id="edit-booking-modal-opener" data-target="#editBooking"style="visibility: hidden;"></a>
                </div>
                <div id="project-report-container1" class="project-report-container">
                    {{--AJAX DATA WILL FILL HERE--}}
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="{{asset_versioned('/js/chosen.jquery.js')}}"></script>
        <!-- fullcalendar js -->
<script src="{{asset_versioned('/js/fullcalendar.js')}}"></script>
<script src="{{asset_versioned('/js/locale-all.js')}}"></script>
<script src="{{asset_versioned('/js/jquery.qtip.js')}}"></script>
<!-- scheduler js -->
<script src="{{asset_versioned('/js/scheduler.js')}}"></script>
<script src="{{asset_versioned('/js/resourceBooking.js')}}"></script>
<script src="{{asset_versioned('/js/editBooking.js')}}"></script>

<script>
$(document).ready(function(){
    $('body').on('click','.report-view-switcher > a',function(){
        var parameters = $('#user-based-report-form').serialize();
        var url = $(this).attr('data-href')+'?'+parameters;
        redirectTo(url);
    });
    $("#filter-skills").chosen({
        no_results_text: "Oops, nothing found with search: ",
        width: "200px"
    }).change(function() {
        fetchReport();
    });
    $('body').on('click','#report-export',function(){
        /*$('#filter-start').val($('#user-report-calendar').fullCalendar('getView').start.format());
        $('#filter-end').val($('#user-report-calendar').fullCalendar('getView').end.format());*/
        var parameters = $('#user-based-report-form').serialize();
        var url = $(this).attr('data-href')+'?'+parameters;
        redirectTo(url);
    });
    $('body').on('submit','#user-based-report-form',function(e){
        e.preventDefault();
        return false;
    });
    $('#report-user-search').keyup(function(){delayFunction(function(){
            fetchReport();
        }, 800 );
    });
});



</script>
<script>
var reportRequest;
function fetchReport(options) {

    $('#user-report-calendar').fullCalendar('refetchResources');
    $('#user-report-calendar').fullCalendar('refetchEvents');
}
function resetReportFilter(){
    $('#report-user-search').val('');

    $("#filter-skills").val('').trigger("chosen:updated");

    fetchReport();
}
$(document).ready(function(){

});

</script>

<script>


    $(function () {
        var allocationRequest = false;
        var resourceRequest = false;
        var calenderSelector = $('#user-report-calendar');
        calenderSelector.fullCalendar({
            schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
            now: moment(),
            selectable: false,
            selectHelper: false,
            eventDurationEditable: false,
            scrollTime: '09:00', // undo default 6am scrollTime
            hiddenDays: [0, 6],
            handleWindowResize: true,
            aspectRatio: 2.5,
            header: {
                left: 'today prev,next',
                center: 'title',
                right: ''
                //right: 'month,timelineMonth'
            },
            buttonText: {
                today: 'Today',
                month: 'Project View',
                timelineMonth: 'Resource View',
            },
            businessHours: {
                // days of week. an array of zero-based day of week integers (0=Sunday)
                dow: [1, 2, 3, 4, 5], // Monday - Friday
                start: '09:00', // a start time (9am in this example)
                end: '18:00', // an end time (6pm in this example)
            },
            defaultView: 'timelineMonth',
            weekends: true,
            minTime: "09:00:00",
            maxTime: "18:00:00",
            resourceLabelText: 'Resources',
            lazyFetching: false,
            refetchResourcesOnNavigate: true,
            resources: function (callback, start, end, timezone) {
                if (resourceRequest && resourceRequest.readyState != 4) {
                    resourceRequest.abort();
                }
                resourceRequest = $.ajax({
                    url: "{{route('reports.user.based.resources')}}",
                    data:{
                        search:$('#report-user-search').val(),
                        skills:$('#filter-skills').val()
                    },
                    type: 'POST',
                    success: function (response) {
                        if (response.status == 'success') {
                            callback(response.data);
                        } else if (response.status == 'error') {

                        }
                    }
                });
            },
            events: function(start, end, timezone, callback) {
                if (allocationRequest && allocationRequest.readyState != 4) {
                    allocationRequest.abort();
                }
                allocationRequest = $.ajax({
                    url: "{{route('reports.user.based.allocations')}}",
                    type: 'POST',
                    data:{
                        start:start.format(),
                        end:end.format(),
                        search:$('#report-user-search').val(),
                        skills:$('#filter-skills').val()
                    },
                    success: function (response) {
                        if (response.status == 'success') {
                            callback(response.data);
                        } else if (response.status == 'error') {

                        }
                    }
                });
            },
            eventRender: function (event, element) {
                element.qtip({
                    content: event.title,
                    position: {
                        my: 'top center',  // Position my top left...
                        at: 'bottom center', // at the bottom right of...
                        target: element // my target
                    }
                });
            }
        });
    });

</script>
@endsection