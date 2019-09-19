@extends('layouts.master')
@section('styles')

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
                        <a>Skill Based</a>
                    </li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="pull-left">
                    <h2>Skill Based Report</h2>
                </div>
                <div class="pull-right"></div>
            </div>
        </div>
        <div class="row report-filter-container">
            <div class="pull-left">
                <form id="skill-based-report-form" method="POST" action="{{route('reports.skill.based.fetch')}}" class="skill-based-report-form form-inline" autocomplete="off">
                <div class="form-group">
                    <div class="col-md-12">
                        <input id="report-user-search" name="report_skill_search" placeholder="Search skill" type="text" class="form-control"/>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <a id="report-filter-reset" class="btn btn-default" onclick="resetReportFilter();"><span class="glyphicon glyphicon-filter"></span> Reset Filter</a>
                        <a id="report-export" data-href="{{route('reports.skill.based.export')}}" class="btn btn-default"><span class="glyphicon glyphicon-download"></span> Export</a>
                    </div>
                </div>

                <input id="report-feed-loaded" value="0" type="hidden">
                <input id="report-feed-page" value="0" type="hidden" name="page">


            </form>
            </div>
            <div class="pull-right">
                <div class="col-md-12">
                    <div class="btn-group report-view-switcher">
                        <a href="#" report-view="detailed" id="grid" class="btn btn-default active"><span class="fa fa-list-alt"></span> Detailed view</a>
                        <a href="#" report-view="line" id="list" class="btn btn-default"><span class="fa fa-bars"></span> Line view</a>
                        </div>
                </div>
            </div>
            <div class="col-md-12"><hr></div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div id="project-report-container" class="project-report-container">
                    {{--AJAX DATA WILL FILL HERE--}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
$(document).ready(function(){
    $('body').on('click','#project-report-container .panel-heading',function(){
        $(this).siblings(".panel-body").toggleClass("toggle-report-display");
    });
    $('body').on('click','#report-export',function(){
        var parameters = $('#skill-based-report-form').serialize();
        var url = $(this).attr('data-href')+'?'+parameters;
        redirectTo(url);
    });
    $('body').on('click','.report-view-switcher [report-view]',function(e){

        $('.report-view-switcher [report-view].active').removeClass('active');
        $('.toggle-report-display').removeClass('toggle-report-display');
        var view = $(this).attr('report-view');
        $('.report-view-switcher [report-view='+view+']').addClass('active');
        if(view=='line'){
            $('#project-report-container').addClass('line-view');
        }else{
            $('#project-report-container').removeClass('line-view');
        }

    });
    $('body').on('submit','#skill-based-report-form',function(e){
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

    if(options===undefined){
        var isLoadMoreRequest = false;
    }else{
        var isLoadMoreRequest = (options.loadMore==undefined)?false:options.loadMore;
    }

    $('#report-feed-loaded').val('0');

    if(isLoadMoreRequest){
        $('#project-report-container').append('<div class="text-center report-feed-loading">Loading more projects..</div>');
    }else{
        $('#report-feed-page').val('0');
        $('#project-report-container').html('<div class="text-center">Loading..</div>');
    }

    if (reportRequest && reportRequest.readyState != 4) {
        reportRequest.abort();
    }

    var filterForm = $('#skill-based-report-form');
    var url = filterForm.attr('action');
    var method = filterForm.attr('method');
    var data = (!isLoadMoreRequest)?filterForm.serialize():filterForm.serialize() + '&loadmore=1';

    reportRequest = $.ajax({
        url: url,
        method: method,
        data: data,
        dataType: "JSON",
        success:function(response){
            if (response.status == 'success'){
                $('#report-feed-page').val(response.data.next_page);
                if(isLoadMoreRequest){
                    $('.report-feed-loading').replaceWith(response.html);
                }else{
                    $('#project-report-container').html(response.html);
                }

                $('#report-feed-loaded').val('1');
            }
        }
    });
}
function resetReportFilter(){
    $('#report-user-search').val('');

    $('#report-feed-loaded').val("0");
    $('#report-feed-page').val("0");

    fetchReport();
}
$(document).ready(function(){
    fetchReport();
    $(window).scroll(function() {
        if($(window).scrollTop() + $(window).height() > $(document).height() - 50) {
            if($('#report-feed-loaded').val()=='1' && $('#report-feed-page').val()!='end') {
                fetchReport({'loadMore':true});
            }
        }
    });
});

$(document).ready(function () {
  $(document).on( "click", ".project-delete-btn", function() {
      var btn = $(this);
      bootbox.confirm({
          message: "Are you sure you want to delete this project?<br>Please note that, all allocations and associated data will be deleted",
          callback: function (result) {
              if (result) {
                  btn.closest('form.project-delete-form').submit();
              }
          }
      });
  });

});

</script>
@endsection
