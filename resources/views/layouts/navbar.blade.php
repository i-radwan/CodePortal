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

                    <li class="{{ Request::is(route(\App\Utilities\Constants::ROUTES_PROBLEMS_INDEX)) ? 'active' : '' }}">
                        <a href="{{ route(\App\Utilities\Constants::ROUTES_PROBLEMS_INDEX) }}">Problems</a>
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
                        <li class="{{ Request::is(route(\App\Utilities\Constants::ROUTES_AUTH_LOGIN)) ? 'active' : '' }}">
                            <a href="{{ route(\App\Utilities\Constants::ROUTES_AUTH_LOGIN) }}">Log In</a>
                        </li>
                        <li class="{{ Request::is(route(\App\Utilities\Constants::ROUTES_AUTH_REGISTER)) ? 'active' : '' }}">
                            <a href="{{ route(\App\Utilities\Constants::ROUTES_AUTH_REGISTER) }}">Sign Up</a>
                        </li>
                    @else
                        {{--Notifications panel--}}
                        @include('layouts.notifications')

                        {{--Profile panel--}}
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()[\App\Utilities\Constants::FLD_USERS_USERNAME] }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <a href="{{ route(\App\Utilities\Constants::ROUTES_PROFILE, Auth::user()[\App\Utilities\Constants::FLD_USERS_USERNAME]) }}">Profile</a>
                                </li>
                                <li role="separator" class="divider">
                                <li>
                                    <a href="{{ route(\App\Utilities\Constants::ROUTES_AUTH_LOGOUT) }}"
                                       onclick="event.preventDefault();
                                       document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>

                                    <form id="logout-form" action="{{ route(\App\Utilities\Constants::ROUTES_AUTH_LOGOUT) }}" method="POST" style="display: none;">
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