@include('modals.create-project')
@include('modals.edit-project')
<script>
$(document).ready(function(){
    $("#page-wrapper").css("min-height", $(document).height()+"px");
});
</script>
<!-- /#wrapper -->
<!-- JS Files -->
<!-- jQuery -->

<script src="{{asset_versioned('js/jquery-ui.min.js')}}"></script>

<!-- Bootstrap Core JavaScript -->
<script src="{{asset_versioned('/js/bootstrap.min.js')}}"></script>
<script src="{{asset_versioned('/js/moment.js')}}"></script>
<script src="{{asset_versioned('js/bootstrap-notify.min.js')}}"></script>
<script src="{{asset_versioned('js/bootbox.js')}}"></script>


<!-- Morris Charts JavaScript -->
<script src="{{asset_versioned('/js/plugins/morris/raphael.min.js')}}"></script><!-- 
<script src="{{asset('/js/plugins/morris/morris.min.js')}}"></script> -->
<!--    <script src="{{asset('/js/plugins/morris/morris-data.js')}}"></script> -->

<!-- Additional page script --> 

<script src="{{asset_versioned('/js/bootstrap-tokenfield.js')}}"></script>
<script src="{{asset_versioned('/js/typeahead.js/dist/typeahead.bundle.min.js')}}"></script>
<script src="{{asset_versioned('/js/bootstrap-datepicker.js')}}"></script>
<script src="{{asset_versioned('/js/common.js')}}"></script>
<script src="{{asset_versioned('/js/createProject.js')}}"></script>
<script src="{{asset_versioned('/js/editProject.js')}}"></script>

<!-- Adding page script -->
@yield('script')

</body>

</html>