@extends('layouts.master')
@section('styles')

<link href="{{asset_versioned('css/daterangepicker.css')}}" rel="stylesheet">
<style>
.morris-hover{position:absolute;z-index:1000;}
.morris-hover.morris-default-style{border-radius:10px;padding:6px;color:#666;background:rgba(255, 255, 255, 0.8);border:solid 2px rgba(230, 230, 230, 0.8);font-family:sans-serif;font-size:12px;text-align:center;}
.morris-hover.morris-default-style .morris-hover-row-label{font-weight:bold;margin:0.25em 0;}
.morris-hover.morris-default-style .morris-hover-point{white-space:nowrap;margin:0.1em 0;}
</style>
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
      </div>
      <div class="white-block" style="text-align:center;">
        <div class="span10 offset1">
            <div id="modalTab">
                <div class="tab-content">
                    <div class="" id="about">
                        <img src="http://via.placeholder.com/150x150" name="aboutme" width="140" height="140" border="0" class="img-circle"></a>
                          <h3 class="media-heading">{{$user->profile->first_name}} {{$user->profile->last_name}}</h3>
                          <h4><small>{{$designation->name}}</small></h4>

                          <small><cite>
                            <i class="glyphicon glyphicon-map-marker"></i>
                            @if($user->profile->address_line1)
                            {{$user->profile->address_line1}},
                            @endif
                            @if($user->profile->address_line2)
                            {{$user->profile->address_line2}}.
                            @endif
                            @if($user->profile->city)
                            {{$user->profile->city}},
                            @endif
                            @if($user->profile->state)
                            {{$user->profile->state}},
                            @endif
                            @if($user->profile->country)
                            {{$user->profile->country}}
                            @endif
                          </cite></small></br>

                          <i class="glyphicon glyphicon-envelope"></i> <a href="mailto:{{$user->email}}">{{$user->email}}</a><br />
                          @if($skills->count())
                            <span><strong>Skills: </strong></span>
                            @foreach ($skills as $skill)
                               <span class="label label-info">{{ $skill->label }}</span>
                            @endforeach
                          @endif



                        </center>
                        <hr>
                        <center>
                        <p class="text-left"><strong>Allocations: </strong><br>
                          @if($user->allocations->count())
                            <table class="table">
                            <thead>
                              <tr>
                                <th>Project</th>
                                <th>Date</th>
                                <th>Allocation</th>
                              </tr>
                            </thead>
                            <tbody>

                            @foreach($user->allocations as $allocation)
                              <tr>
                                <td>{{$allocation->project->name}}</td>
                                <td>{{ \Carbon\Carbon::parse($allocation->start_date)->toFormattedDateString()}} to {{ \Carbon\Carbon::parse($allocation->end_date)->toFormattedDateString()}}</td>
                                <td>{{$allocation->allocation_value}}%</td>
                              </tr>
                            @endforeach
                            </tbody>
                          </table>
                          @else
                            <center><small>No Allocations found</small></center>
                          @endif
                        </center>
                        <hr>
                        <center>
                          <div class="row">
                            <p class="text-left"><strong>Current Allocations: </strong><br>
                          </div>
                          <div class="row">
                            @permission('resource-list-allocate')
                            <div id="reportrange" title="Select a Range" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                                <span>Select a Range</span> <b class="caret"></b>
                            </div>
                            @endpermission
                          </div>

                            <div class="current-allocation-graph" id="current-allocation-graph">

                            </div>
                        </center>
                  </div>



              </div>
            </div>
            </div>

      </div>
    </div>


@endsection

@section('script')
<script src="{{asset_versioned('/js/plugins/morris/raphael.min.js')}}"></script>
<script src="{{asset_versioned('/js/plugins/morris/morris.js')}}"></script>
<script src="{{asset_versioned('js/daterangepicker.js')}}"></script>
<script type="text/javascript">

function fetchAllocationData(dochart){
    $.ajax({
        url: '{{route('resources.currentallocationdata', ["id"=>$user->id])}}',
        dataType: 'JSON',
        type: 'GET',
        success: function(response) {
            if(response.status=='success') {
              document.getElementById('current-allocation-graph').innerHTML = "";
              var dochart = new Morris.Bar({
                  element: 'current-allocation-graph',
                  xkey: 'x',
                  ykeys: response.keys,
                  labels:response.label,
                  behaveLikeLine: true,
                  resize: true,
                  stacked:true,
                  data:response.data,
                  hideHover: 'auto',
                  hoverCallback: function(index, options, content) {
                    var data = options.data[index];
                    var label = options.labels;
                    var colors = options.barColors;
                    var legendContent = "";
                    var legendHeader = "";
                    var totalAllocationPerDay = 0;
                    var customLegend = document.createElement("div");

                    $.each(data, function(index, value) {
                        if(value > 0){
                          legendContent += '<div class="morris-hover-point" style="color: '+colors[index]+'">'+ label[index] +' : <b>'+ value +'%</b></div>'
                          totalAllocationPerDay += value;
                        }
                    });

                    legendHeader = '<div class="morris-hover-row-label">'+data.x+'</div><div>Total Allocation <b>'+totalAllocationPerDay+' %</b></div>';
                    customLegend.innerHTML = legendHeader.concat(legendContent);
                    return(customLegend);
                  }
              });
            }
        }
    });
}
function initDateRangePicker(){
  $('#reportrange').daterangepicker({
      /*startDate: start,
       endDate: end,*/
      ranges: {
          'Today': [moment(), moment()],
          'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days': [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month': [moment().startOf('month'), moment().endOf('month')],
          'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      },
      "locale": {
          "format": "YYYY-MM-DD",
          "cancelLabel": "Reset"
      },
      opens:'left'
  }).on('apply.daterangepicker', function (ev, picker){

      var start = picker.startDate;
      var end = picker.endDate;

      $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

      var data = [];
      if(start){
        data.push({ name: "start", value: start.format('YYYY-MM-DD') });
      }
      if(end){
        data.push({ name: "end", value: end.format('YYYY-MM-DD') });
      }
      $.ajax({
          url: '{{route('resources.currentallocationdata', ["id"=>$user->id])}}',
          dataType: 'JSON',
          type: 'GET',
          data:data,
          success: function(response) {
              if(response.status=='success') {
                console.log(Object.keys(response.data).length);
                document.getElementById('current-allocation-graph').innerHTML = "";
                if(Object.keys(response.data).length){
                  var dochart = new Morris.Bar({
                      element: 'current-allocation-graph',
                      xkey: 'x',
                      ykeys: response.keys,
                      labels:response.label,
                      behaveLikeLine: true,
                      resize: false,
                      stacked:true,
                      data:response.data,
                      hideHover: 'auto',
                      hoverCallback: function(index, options, content) {
                        var data = options.data[index];
                        var label = options.labels;
                        var colors = options.barColors;
                        var legendContent = "";
                        var legendHeader = "";
                        var totalAllocationPerDay = 0;
                        var customLegend = document.createElement("div");

                        $.each(data, function(index, value) {
                            if(value > 0){
                              legendContent += '<div class="morris-hover-point" style="color: '+colors[index]+'">'+ label[index] +' : <b>'+ value +'%</b></div>'
                              totalAllocationPerDay += value;
                            }
                        });

                        legendHeader = '<div class="morris-hover-row-label">'+data.x+'</div><div>Total Allocation <b>'+totalAllocationPerDay+' %</b></div>';
                        customLegend.innerHTML = legendHeader.concat(legendContent);
                        return(customLegend);
                      }

                  });
                }


              }
          }
      });

  }).on('cancel.daterangepicker', function (ev, picker) {

      $('#reportrange span').html($(this).attr('title'));

  });
}

$(document).ready(function(){

    fetchAllocationData();
    initDateRangePicker();

});
</script>
@endsection
