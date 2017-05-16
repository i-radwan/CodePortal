@php
    use App\Utilities\Constants;

    $isProfileOwner = (Auth::check() && Auth::user()[Constants::FLD_USERS_ID] == $user[Constants::FLD_USERS_ID]);
    $username = $user[Constants::FLD_USERS_USERNAME];

@endphp

{!! \ConsoleTVs\Charts\Facades\Charts::assets() !!}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="panel panel-default">

            {{--Edit buttons--}}
            @if($isProfileOwner)
                <a href="{{ route(\App\Utilities\Constants::ROUTES_PROFILE_EDIT) }}"
                   class="btn btn-link text-dark pull-right margin-5px">Edit</a>
            @endif

            <div class="panel-heading">Profile</div>

            <div class="row card margin-10px">

                {{--Horizontal Nav tabs--}}
                <ul class="nav nav-pills nav-stacked col-sm-3" role="tablist">
                    <li {{ ($view == "info") ? 'class=active' : '' }}>
                        <a href="{{ route(\App\Utilities\Constants::ROUTES_PROFILE, $username) }}">
                            Profile
                        </a>
                    </li>
                    <li {{ ($view == "problems") ? 'class=active' : '' }}>
                        <a href="{{ route(\App\Utilities\Constants::ROUTES_PROFILE_PROBLEMS, $username) }}">
                            Problems
                        </a>
                    </li>
                    <li {{ ($view == "contests") ? 'class=active' : '' }}>
                        <a href="{{ route(\App\Utilities\Constants::ROUTES_PROFILE_CONTESTS, $username) }}">
                            Contests
                        </a>
                    </li>
                    <li {{ ($view == "groups") ? 'class=active' : '' }}>
                        <a href="{{ route(\App\Utilities\Constants::ROUTES_PROFILE_GROUPS, $username) }}">
                            Groups
                        </a>
                    </li>
                    <li {{ ($view == "teams") ? 'class=active' : '' }}>
                        <a href="{{ route(\App\Utilities\Constants::ROUTES_PROFILE_TEAMS, $username) }}">
                            Teams
                        </a>
                    </li>
                    <li {{ ($view == "blogs") ? 'class=active' : '' }}>
                        <a href="{{ route(\App\Utilities\Constants::ROUTES_PROFILE_BLOGS, $username) }}">
                            Blogs
                        </a>
                    </li>
                </ul>

                <!-- Nav tabs contents -->
                <div class="tab-content col-sm-9">
                    <!-- User info tab -->
                    <div role="tabpanel" class="fade in tab-pane active horizontal-scroll container" id="userInfo">

                        {{--User profile info--}}
                        @if($view == "info")
                            @include('profile.user_info')
                            {!! $chart->render() !!}

                            {{--User problems--}}
                        @elseif($view == "problems")
                            @include('profile.problems')

                            {{--User contests--}}
                        @elseif($view == "contests")
                            @include('profile.contests')

                            {{--User groups--}}
                        @elseif($view == "groups")
                            @include('profile.groups')

                            {{--User groups--}}
                        @elseif($view == "blogs")
                            {{--@include('groups.groups_table')--}}
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
