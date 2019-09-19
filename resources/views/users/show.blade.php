@extends('layouts.master')
@section('styles')
<!-- fullcalendar css -->
<link href="{{asset('/css/fullcalendar.print.css')}}" rel="stylesheet" media="print">
<link href="{{asset('/css/fullcalendar.css')}}" rel="stylesheet">
<link href="{{asset('/css/jquery.qtip.css')}}" rel="stylesheet">
<!-- scheduler css -->
<link href="{{asset('/css/scheduler.css')}}" rel="stylesheet">
@endsection
@section('content')
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <ol class="breadcrumb">
                    <li>
                        <i class="fa fa-users"></i> <a href="{{ route('resources.index') }}">Resources</a>
                    </li>
                    <li class="active">
                        <i class="fa fa-user"></i> {{$user->profile->first_name}} {{$user->profile->last_name}}
                    </li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2> Allocations - {{$user->profile->first_name}} {{$user->profile->last_name}}</h2>
                </div>
            </div>
        </div>
        <div class="white-block">

            @if($user->allocatable==0)
                <h3>This user is not allocatable</h3>
            @elseif($user->disabled==1)
                <h3>This user is disabled</h3>
            @elseif($user->present()->projectCount()==0)
                <h3>No projects allocated for this user</h3>
            @else
                <div id='calendar'></div>
            @endif
            <input type="hidden" id="resource_id" value="{{$user->id}}" />
            <a data-toggle="modal" id="booking-modal-opener" data-target="#newBooking"style="visibility: hidden;"></a>
            <a data-toggle="modal" id="edit-booking-modal-opener" data-target="#editBooking"style="visibility: hidden;"></a>
        </div>
    </div>
</div>
@include('modals.create-booking')
@include('modals.edit-booking')
@endsection
@section('script')
        <!-- fullcalendar js -->
        <script src="{{asset_versioned('/js/fullcalendar.js')}}"></script>
        <script src="{{asset_versioned('/js/locale-all.js')}}"></script>
        <script src="{{asset_versioned('/js/jquery.qtip.js')}}"></script>
        <!-- scheduler js -->
        <script src="{{asset_versioned('/js/scheduler.js')}}"></script>
        <script src="{{asset_versioned('/js/resourceBooking.js')}}"></script>
        <script src="{{asset_versioned('/js/editBooking.js')}}"></script>
        <script>
        $(function () {
            $('#calendar').fullCalendar({
                schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
                now: moment(),
                editable: false, // enable draggable events
                @if(Auth::user()->can(['resource-list-allocate']))
                    selectable: true,
                    selectHelper: true,
                    eventDurationEditable: true,
                @else
                    selectable: false,
                    selectHelper: false,
                    eventDurationEditable: false,
                @endif
                scrollTime: '09:00', // undo default 6am scrollTime
                hiddenDays: [0, 6],
                handleWindowResize: true,
                aspectRatio: 2.5,
                header: {
                    left: 'prev,next',
                    center: 'title',
                    right: ''
                    //right: 'timelineMonth,timelineYear'
                },
                businessHours: {
                    // days of week. an array of zero-based day of week integers (0=Sunday)
                    dow: [1, 2, 3, 4, 5], // Monday - Thursday

                    start: '09:00', // a start time (10am in this example)
                    end: '18:00', // an end time (6pm in this example)
                },
                defaultView: 'timelineMonth',
                weekends: true,
                minTime: "09:00:00",
                maxTime: "18:00:00",
                resourceLabelText: 'Projects',
                refetchResourcesOnNavigate: true,
                lazyFetching : false,
                resources: {
                    url: '/resources/getprojects',
                    type: 'GET',
                    data: {
                        user_id: '{{$user->id}}'
                    },
                    error: function() {
                        alert('There was an error while fetching resources!');
                    }
                },
                eventSources: [{
                    url: '/resources/getallocations',
                    type: 'GET',
                    data: {
                        user_id: '{{$user->id}}'
                    },
                    error: function() {
                        alert('There was an error while fetching events!');
                    }
                }],
               @if(Auth::user()->can(['resource-list-allocate']))
                    select: function (start, end, jsEvent, view, resource) {
                        openBookingWindow($('#resource_id').val(),"{{$user->profile->first_name}} {{$user->profile->last_name}}", resource.id, resource.title, start.format(),end.format());
                    },
                    eventClick: function (calEvent, jsEvent, view) {
                        openEditBookingWindow(calEvent.id);
                    },
                @endif
                eventMouseover: function (calEvent, jsEvent, view) {
                    // change the border color just for fun
                    $(this).css('opacity', '0.5');
                },
                eventMouseout: function (calEvent, jsEvent, view) {
                    $(this).css('opacity', '1');
                },
                eventRender: function(event, element) {
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