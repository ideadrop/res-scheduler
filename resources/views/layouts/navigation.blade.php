<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{route('home')}}">{{appName()}}</a>
            </div>
            <!-- Top Menu Items -->
            <ul class="nav navbar-right top-nav">
                @if (Auth::guest())
                        <li><a href="{{ route('auth.login') }}">Login</a></li>
                @else
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> {{ $authUser->profile->first_name }} {{ $authUser->profile->last_name }} <b class="caret"></b></a>
                    <ul class="dropdown-menu">
<!--                        <li>
                            <a href="#"><i class="fa fa-fw fa-user"></i> Profile</a>
                        </li>-->
<!--                        <li>
                            <a href="#"><i class="fa fa-fw fa-envelope"></i> Inbox</a>
                        </li>-->
<!--                        <li>
                            <a href="#"><i class="fa fa-fw fa-gear"></i> Settings</a>
                        </li>
                        <li class="divider"></li>-->
                        <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
                    </ul>
                </li>
                @endif
            </ul>
            <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav side-nav">
                    @if (!Auth::guest())
                        @if($authUser->can(['view-dashboard']))
                        <li class="">
                            <a href="{{ route('dashboard') }}"><i class="fa fa-fw fa-dashboard"></i>Dashboard</a>
                        </li>
                        @endif
                        @if($authUser->can(['project-create', 'project-list-allocate', 'view-my-project-calender']))
                        <li>
                            <a href="javascript:;" data-toggle="collapse" data-target="#demo"><i class="fa fa-tasks"></i> Projects <i class="fa fa-fw fa-caret-down"></i></a>
                            <ul id="demo" class="collapse">

                                @if($authUser->can(['project-list-allocate']))
                                <li>
                                    <a href="{{ route('project.list') }}"><i class="icon fa fa-list"></i> List Projects</a>
                                </li>
                                @endif
                                @if($authUser->can(['view-my-project-calender']))
                                <li>
                                    <a href="{{ route('resources.show',$authUser->id) }}"><i class="icon fa fa-list"></i> My Projects</a>
                                </li>
                                @endif
                                @if($authUser->can(['project-create']))
                                <li>
                                    <a href="#" data-toggle="modal" data-target="#newProject"><i class="icon fa fa-plus"></i> New Project</a>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        @if($authUser->can(['resource-create', 'resource-list-allocate']))
                        <li>
                            <a href="javascript:;" data-toggle="collapse" data-target="#demo1"><i class="fa fa-users"></i> Resources <i class="fa fa-fw fa-caret-down"></i></a>
                            <ul id="demo1" class="collapse">

                                @if($authUser->can(['resource-list-allocate']))
                                <li>
                                    <a href="{{ route('resources.index') }}"><i class="icon fa fa-list"></i> List Resources</a>
                                </li>
                                @endif
                                
                                @if($authUser->hasRole(['admin']))
                                    <li>
                                        <a href="{{ route('resources.csv.upload') }}"><i class="icon fa fa-plus"></i> CSV Import</a>
                                    </li>
                                @endif

                                @if($authUser->can(['resource-create']))
                                <li>
                                    <a href="{{ route('resources.create') }}"><i class="icon fa fa-plus"></i> New Resource</a>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        @if($authUser->can(['role-create', 'role-list']))
                        <li>
                            <a href="javascript:;" data-toggle="collapse" data-target="#demo2"><i class="fa fa-gears"></i> Roles <i class="fa fa-fw fa-caret-down"></i></a>
                            <ul id="demo2" class="collapse">

                                @if($authUser->can(['role-list']))
                                <li>
                                    <a href="{{ route('roles.index') }}"><i class="icon fa fa-list"></i> List Roles</a>
                                </li>
                                @endif
                                @if($authUser->can(['role-create']))
                                <li>
                                    <a href="{{ route('roles.create') }}"><i class="icon fa fa-plus"></i> New Role</a>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        @if($authUser->can(['view-report']))
                            <li>
                                <a href="#" data-toggle="collapse" data-target="#report-left-nav-list"><i class="fa fa-bar-chart"></i> Reports <i class="fa fa-fw fa-caret-down"></i></a>
                                <ul id="report-left-nav-list" class="collapse">
                                    <li>
                                        <a href="{{ route('reports.project.based') }}"><i class="icon fa fa-bar-chart"></i> Project Based</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('reports.user.based') }}"><i class="icon fa fa-bar-chart"></i> User Based</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('reports.skill.based') }}"><i class="icon fa fa-bar-chart"></i> Skill Based</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('reports.tag.based') }}"><i class="icon fa fa-bar-chart"></i> Tag Based</a>
                                    </li>

                                </ul>
                            </li>
                            @endif
                    @endif

<!--                    <li>
                        <a href="charts.html"><i class="fa fa-fw fa-bar-chart-o"></i> Charts</a>
                    </li>
                    <li>
                        <a href="tables.html"><i class="fa fa-fw fa-table"></i> Tables</a>
                    </li>
                    <li>
                        <a href="forms.html"><i class="fa fa-fw fa-edit"></i> Forms</a>
                    </li>
                    <li>
                        <a href="bootstrap-elements.html"><i class="fa fa-fw fa-desktop"></i> Bootstrap Elements</a>
                    </li>
                    <li>
                        <a href="bootstrap-grid.html"><i class="fa fa-fw fa-wrench"></i> Bootstrap Grid</a>
                    </li>
                    <li>
                        <a href="javascript:;" data-toggle="collapse" data-target="#demo"><i class="fa fa-fw fa-arrows-v"></i> Dropdown <i class="fa fa-fw fa-caret-down"></i></a>
                        <ul id="demo" class="collapse">
                            <li>
                                <a href="#">Dropdown Item</a>
                            </li>
                            <li>
                                <a href="#">Dropdown Item</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="blank-page.html"><i class="fa fa-fw fa-file"></i> Blank Page</a>
                    </li>
                    <li>
                        <a href="index-rtl.html"><i class="fa fa-fw fa-dashboard"></i> RTL Dashboard</a>
                    </li>-->
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </nav>
