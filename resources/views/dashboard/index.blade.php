@extends('layouts.master')
@section('styles')
<!-- Morris Charts CSS -->
<link href="{{asset('/css/plugins/morris.css')}}" rel="stylesheet">
@endsection
@section('content')
<div id="page-wrapper">
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header hidden">
                    Dashboard <small>Statistics Overview</small>
                </h1>
                <ol class="breadcrumb">
                    <li class="active">
                        <i class="fa fa-dashboard"></i> Dashboard
                    </li>
                </ol>
            </div>
        </div>
        <!-- /.row -->
        @if(1==2)
        @include('dashboard.partials.dashboard-tab-blocks')
        @endif

        @include('dashboard.partials.dashboard-donuts-charts')

        <div class="row">

            @include('dashboard.partials.dashboard-active-projects')

            @include('dashboard.partials.dashboard-free-users')

        </div>
        <!-- /.row -->

    </div>
    <!-- /.container-fluid -->

</div>
<!-- /#page-wrapper -->

@endsection
@section('script')
<script src="{{asset_versioned('/js/plugins/morris/raphael.min.js')}}"></script>
<script src="{{asset_versioned('/js/plugins/morris/morris.js')}}"></script>
<script type="text/javascript">
    function fetchDashboardActiveProjects(){
        $('#dashboard-active-projects-container').html('<tr><td colspan="3"><div class="well text-center">Loading...</div></td></tr>');

        $.ajax({
            url: '{{route('dashboard.active.projects')}}',
            dataType: 'JSON',
            type: 'POST',
            success: function(response) {
                if(response.status=='success') {
                    $('#dashboard-active-projects-container').html(response.html);
                }
            }
        });
    }
    function fetchDashboardFreeResources(){
        $('#dashboard-free-users-container').html('<tr><td colspan="4"><div class="well text-center">Loading...</div></td></tr>');

        $.ajax({
            url: '{{route('dashboard.free.resources')}}',
            dataType: 'JSON',
            type: 'POST',
            success: function(response) {
                if(response.status=='success') {
                   $('#dashboard-free-users-container').html(response.html);
                }
            }
        });
    }
    function fetchDonutData(type){
        $.ajax({
            url: '{{route('dashboard.skill.donut')}}',
            dataType: 'JSON',
            type: 'POST',
            data: {type: type},
            success: function(response) {
                if(response.status=='success') {
                    new Morris.Donut({
                        element: 'donut-chart-'+type,
                        resize: true,
                        data: response.data,
                        hideHover: 'auto'
                    });
                }
            }
        });
    }
    $(document).ready(function(){
        fetchDonutData('project');
        fetchDonutData('user');
        fetchDashboardActiveProjects();
        fetchDashboardFreeResources();
    });
</script>
@endsection