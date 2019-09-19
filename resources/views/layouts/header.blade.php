<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Cubet - Resource Scheduler</title>
        <!-- Bootstrap Core CSS -->
        <link href="{{asset('/css/bootstrap.min.css')}}" rel="stylesheet">
        <!-- Custom CSS -->
        <link href="{{asset('/css/sb-admin.css')}}" rel="stylesheet">        
        <!-- Custom Fonts -->
        <link href="{{asset('/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css">    
        <link  href="{{asset('/css/bootstrap-datepicker.min.css')}}" rel="stylesheet">
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <!--<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>-->
        <![endif]-->       
        <!-- Tokenfield CSS -->
        <link type="text/css"  href="{{asset('/css/tokenfield-typeahead.css')}}" rel="stylesheet">
        <link type="text/css"  href="{{asset('/css/bootstrap-tokenfield.min.css')}}" rel="stylesheet">
        <link type="text/css"  href="{{asset('/css/common.css')}}" rel="stylesheet">
        @yield('styles')
        <style>
            .rss-alert{
                text-align: left;
            }
        </style>
        <script src="{{asset_versioned('js/jquery.min.js')}}"></script>
        <script>
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        </script>
    </head>
    <body>
        <div id="wrapper">