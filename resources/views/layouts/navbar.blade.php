<header>
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">
                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name') }}
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
                    <li class="{{ Request::is('/') ? 'active' : '' }}">
                        <a href="{{ url('/') }}">
                            Home
                            {!! Request::is('/') ? '<span class="sr-only">(current)</span>' : '' !!}
                        </a>
                    </li>

                    <li class="{{ Request::is('contests') ? 'active' : '' }}">
                        <a href="{{ url('contests') }}">
                            Contests
                            {!! Request::is('contests') ? '<span class="sr-only">(current)</span>' : '' !!}
                        </a>
                    </li>

                    <li class="{{ Request::is('problems') ? 'active' : '' }}">
                        <a href="{{ url('problems') }}">
                            Problems
                            {!! Request::is('problems') ? '<span class="sr-only">(current)</span>' : '' !!}
                        </a>
                    </li>

                    <li class="{{ Request::is('blogs') ? 'active' : '' }}">
                        <a href="{{ url('blogs') }}">
                            Blogs
                            {!! Request::is('blogs') ? '<span class="sr-only">(current)</span>' : '' !!}
                        </a>
                    </li>

                    <li class="{{ Request::is('groups') ? 'active' : '' }}">
                        <a href="{{ url('groups') }}">
                            Groups
                            {!! Request::is('groups') ? '<span class="sr-only">(current)</span>' : '' !!}
                        </a>
                    </li>

                    {{--<li class="dropdown">--}}
                        {{--<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Extras <span class="caret"></span></a>--}}
                        {{--<ul class="dropdown-menu">--}}
                            {{--<li><a href="#">Contact Us</a></li>--}}
                            {{--<li role="separator" class="divider"></li>--}}
                            {{--<li><a href="#">About</a></li>--}}
                        {{--</ul>--}}
                    {{--</li>--}}
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        <li><a href="{{ route('login') }}">Log In</a></li>
                        <li><a href="{{ route('register') }}">Sign Up</a></li>
                    @else
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <a href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                       document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
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