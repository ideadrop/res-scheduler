@extends('layouts.master')
@section('styles')
        <!-- fullcalendar css -->
<link href="{{asset_versioned('/css/fullcalendar.print.css')}}" rel="stylesheet" media="print">
<link href="{{asset_versioned('/css/fullcalendar.css')}}" rel="stylesheet">
<link href="{{asset_versioned('/css/jquery.qtip.css')}}" rel="stylesheet">
<!-- scheduler css -->
<link href="{{asset_versioned('/css/scheduler.css')}}" rel="stylesheet">
@endsection
@section('content')
    <div id="page-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <ol class="breadcrumb">
                        <li>
                            <i class="fa fa-list"></i> <a href="{{ route('project.list') }}">Projects</a>
                        </li>
                        <li class="active">
                            <i class="fa fa-users"></i> {{$project->name}}
                        </li>
                    </ol>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <h2> {{ $project->name }} </h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div id='project-calendar' class="project-calendar"></div>
                </div>
            </div>
        </div>
    </div>
    <form autocomplete="off">
        <input type="hidden" id="project-id" value="{{$project->id}}">
    </form>
    @include('modals.create-project-allocation')
    @include('modals.edit-project-allocation')
@endsection
@section('script')
    <script src="{{asset_versioned('/js/jquery.qtip.js')}}"></script>
    <!-- fullcalendar js -->
    <script src="{{asset_versioned('/js/fullcalendar.js')}}"></script>
    <script src="{{asset_versioned('/js/locale-all.js')}}"></script>
    <!-- scheduler js -->
    <script src="{{asset_versioned('/js/scheduler.js')}}"></script>
    <script src="{{asset_versioned('/js/project-resource-booking.js')}}"></script>
    <script>


        $(function () {
            $('#project-calendar').fullCalendar({
                schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
                now: moment(),
                selectable: true,
                selectHelper: true,
                eventDurationEditable: true,
                scrollTime: '09:00', // undo default 6am scrollTime
                hiddenDays: [0, 6],
                handleWindowResize: true,
                aspectRatio: 2.5,
                header: {
                    left: 'today prev,next',
                    center: 'title',
                    right: 'month,timelineMonth'
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
                defaultView: 'month',
                weekends: true,
                minTime: "09:00:00",
                maxTime: "18:00:00",
                resourceLabelText: 'Resources',
                lazyFetching: false,
                refetchResourcesOnNavigate: true,
                resources: function (callback, start, end, timezone) {
                    $.ajax({
                        url: "/project/resources/{{$project->id}}",
                        type: 'POST',
                        success: function (response) {
                            if (response.status == 'success') {
                                callback(response.data);
                            } else if (response.status == 'error') {

                            }
                        }
                    });
                },
                events: {
                    url: '/project/allocations/{{$project->id}}'
                },

                select: function (start, end, jsEvent, view, resource) {
                    if (view.type == 'month') {
                        openAllocationWindow(start.format(), end.format());
                    } else if (view.type == 'timelineMonth') {
                        openAllocationWindow(start.format(), end.format(), resource.id);
                    }
                },
                eventClick: function (calEvent, jsEvent, view) {
                    openEditAllocationWindow(calEvent);
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