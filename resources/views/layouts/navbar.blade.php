<header>
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">
                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    <strong>{{ config('app.name') }}</strong>
                </a>

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    <li class="{{ Request::is('contests') ? 'active' : '' }}">
                        <a href="{{ url('contests') }}">Contests</a>
                    </li>

                    <li class="{{ Request::is('problems') ? 'active' : '' }}">
                        <a href="{{ url('problems') }}">Problems</a>
                    </li>

                    <li class="{{ Request::is('blogs') ? 'active' : '' }}">
                        <a href="{{ url('blogs') }}">Blogs</a>
                    </li>

                    <li class="{{ Request::is('groups') ? 'active' : '' }}">
                        <a href="{{ url('groups') }}">Groups</a>
                    </li>
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        <li class="{{ Request::is('login') ? 'active' : '' }}">
                            <a href="{{ route('login') }}">Log In</a>
                        </li>
                        <li class="{{ Request::is('register') ? 'active' : '' }}">
                            <a href="{{ route('register') }}">Sign Up</a>
                        </li>
                    @else
                        {{--Notifications panel--}}
                        @php($notifications = Auth::user()->receivedNotifications()->orderBy(\App\Utilities\Constants::FLD_NOTIFICATIONS_ID, 'desc')->get())
                        @php($unreadCount = count(Auth::user()->unreadNotifications()->get()))

                        @if(count($notifications))
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                   aria-expanded="false">
                                    <i id="notifications-icon"
                                       class="notifications-icon fa fa-bell{{($unreadCount > 0)?' dark-red':'-o'}}"
                                       aria-hidden="true"></i>
                                    <span class="notifications-text">Notifications</span>
                                </a>

                                <ul class="dropdown-menu notifications" role="menu">
                                    @foreach($notifications as $notification)
                                        @php($contest = \App\Models\Contest::find($notification->resource_id))
                                        <li class="notification-container">

                                            <a href="{{url('contest/'.$contest->id)}}">
                                                <div class="notification-icon">
                                                    <i class="fa fa-flag-checkered" aria-hidden="true"></i>
                                                </div>
                                                <div class="notification-text">
                                                    <span>{{\App\Utilities\Constants::NOTIFICATION_TEXT[$notification->type]}}
                                                        <span class="notification-resource-name">{{$contest->name}}</span></span>
                                                </div>
                                            </a>
                                        </li>
                                        @if(!$loop->last)
                                            <li role="separator" class="divider">@endif
                                    @endforeach
                                </ul>
                            </li>
                        @endif
                        {{--/Notifications panel--}}

                        {{--Profile panel--}}
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-expanded="false">
                                {{ Auth::user()->username }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <a href="{{ url('profile/' . Auth::user()->username) }}">Profile</a>
                                </li>
                                <li role="separator" class="divider">
                                <li>
                                    <a href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                       document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                          style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
</header>