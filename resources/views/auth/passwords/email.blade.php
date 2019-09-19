<html>


<head>
    <title>{{appName()}}</title>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <style>

        @import url('https://fonts.googleapis.com/css?family=Raleway:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&subset=latin-ext');

        #playground-container {
            height: 500px;
            overflow: hidden !important;

        }

        .main {
            margin-top: 40px;
            -webkit-box-shadow: 0px 0px 14px 0px rgba(0, 0, 0, 0.24);
            -moz-box-shadow: 0px 0px 14px 0px rgba(0, 0, 0, 0.24);
            box-shadow: 0px 0px 14px 0px rgba(0, 0, 0, 0.24);
            padding: 0px;
            /*background:#2196f3;*/
            background: #91B1B0;
        }

        .fb:focus, .fb:hover {
            color: #FFF !important;
        }

        body {
            font-family: 'Raleway', sans-serif;
        }

        .left-side {
            padding: 0px 0px 0px;

            background-size: cover;
        }

        .left-side h3 {
            font-size: 30px;
            font-weight: 900;
            color: #FFF;
            padding: 50px 10px 00px 26px;
        }

        .left-side p {
            font-weight: 600;
            color: #FFF;
            padding: 10px 10px 10px 26px;
        }

        .fb {
            /*background: #2d6bb7;*/
            background: #91B1B0;
            color: #FFF;
            padding: 10px 15px;
            border-radius: 18px;
            font-size: 12px;
            font-weight: 600;
            margin-right: 15px;
            margin-left: 26px;
            -webkit-box-shadow: 0px 0px 14px 0px rgba(0, 0, 0, 0.24);
            -moz-box-shadow: 0px 0px 14px 0px rgba(0, 0, 0, 0.24);
            box-shadow: 0px 0px 14px 0px rgba(0, 0, 0, 0.24);
        }

        .tw {
            background: #20c1ed;
            color: #FFF;
            padding: 10px 15px;
            border-radius: 18px;
            font-size: 12px;
            font-weight: 600;
            margin-right: 15px;
            -webkit-box-shadow: 0px 0px 14px 0px rgba(0, 0, 0, 0.24);
            -moz-box-shadow: 0px 0px 14px 0px rgba(0, 0, 0, 0.24);
            box-shadow: 0px 0px 14px 0px rgba(0, 0, 0, 0.24);
        }

        .right-side {
            padding: 0px 0px 0px;
            background: #FFF;
            background-size: cover;
            min-height: 514px;
        }

        .right-side h3 {
            font-size: 30px;
            font-weight: 700;
            color: #000;
            padding: 50px 10px 0px 35px;
        }

        .right-side p {
            font-weight: 600;
            color: #000;
            padding: 10px 50px 10px 50px;
        }

        .form {
            padding: 10px 50px 10px 50px;
        }

        .form-control {
            box-shadow: none !important;
            border-radius: 0px !important;
            border-bottom: 1px solid #91B1B0 !important;
            /*border-bottom: 1px solid #2196f3 !important;*/
            border-top: 1px !important;
            border-left: none !important;
            border-right: none !important;
        }

        .btn-deep-purple {
            background: #91B1B0;
            /*background: #2196f3;*/
            border-radius: 18px;
            padding: 5px 19px;
            color: #FFF;
            font-weight: 600;
            float: right;
            -webkit-box-shadow: 0px 0px 14px 0px rgba(0, 0, 0, 0.24);
            -moz-box-shadow: 0px 0px 14px 0px rgba(0, 0, 0, 0.24);
            box-shadow: 0px 0px 14px 0px rgba(0, 0, 0, 0.24);
        }

        .login-bottom-img {
            background: url('http://cubettech.com/wp-content/themes/cubettech/img/bg_vector.png');
        }

        .login-app-name {
            color: #FFF;
            padding: 10px 33px;
        }
        .form-control.input-lg {
            padding-left: 0;
        }
    </style>

</head>
<body>
<div class="container">

    <div class="col-md-10 col-md-offset-1 main">
        <div class="col-md-6 left-side">

            <h3>
                <div class="login-logo-container">
                    <a href="{{route('home')}}">
                    <img src="http://cubettech.com/wp-content/themes/cubettech/img/cubet-logo.svg" alt="Cubet Logo"
                         title="Cubet Techno Labs" class="dark" itemprop="logo">
                    </a>
                </div>
            </h3>
            <h4 class="login-app-name">{{appName()}}</h4>
            <br>


        </div>
        <!--col-sm-6-->

        <div class="col-md-6 right-side">
            <h3>Get a reset password link</h3>
            <!--Form with header-->

            {!! Form::open(array('url' => url("/password/email"),'method'=>'POST','role'=>'form','class'=>'form-horizontal')) !!}
            <div class="form">
                <div class="form-group" id="login-errors">
                    @include('layouts.notifications')
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <input id="email" type="email" class="form-control input-lg" name="email" value="{{ old('email') }}" placeholder="Email">
                </div>
                <div class="form-group">
                    <div class="pull-right">
                        <button class="btn btn-deep-purple" type="submit"><span class="glyphicon glyphicon-envelope"></span> Request</button>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
            <!--/Form with header-->

        </div>
        <!--col-sm-6-->


    </div>
    <!--col-sm-8-->

</div>
<!--container-->
<script>
document.getElementsByClassName('close')[0].addEventListener('click', function (event) {
    document.getElementById("login-errors").innerHTML = "";
});
</script>
</body>
</html>