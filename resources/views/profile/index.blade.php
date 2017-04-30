@php
    use App\Utilities\Constants;
    Charts::assets();
    $authUser = Auth::user();
    $isAuth = ($authUser != null && $authUser[Constants::FLD_USERS_ID] == $user[Constants::FLD_USERS_ID]);
    $solvedProblems = $user->problems(true)->paginate(Constants::PROBLEMS_COUNT_PER_PAGE);
    $unsolvedProblems = $user->problems(false)->paginate(Constants::PROBLEMS_COUNT_PER_PAGE);
@endphp

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">Profile</div>

            @if($isAuth)
                <a href="{{ route(\App\Utilities\Constants::ROUTES_PROFILE_EDIT) }}"
                   class="btn btn-link pull-right btn-sm RbtnMargin "
                   role="button">
                    Edit
                    <i class="fa fa-gear"></i>
                </a>
            @endif

            <div class="content-tabs card margin-5px">

                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li class=" nav-item active" role="presentation">
                        <a href="#userInfo" role="tab" data-toggle="tab">User Info</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#userActivity" role="tab" data-toggle="tab">User Activity</a>
                    </li>
                    <li class="nav-item " role="presentation">
                        <a href="#problems" role="tab" data-toggle="tab">Problems</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#contests" role="tab" data-toggle="tab">Contests</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#groups" role="tab" data-toggle="tab">Groups</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#teams" role="tab" data-toggle="tab">Teams</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- User info tab -->
                    <div role="tabpanel" class="fade in tab-pane active " id="userInfo">
                        @include('profile.user_info')
                    </div>

                    <!-- Problems tab -->
                    <div role="tabpanel" class="fade tab-pane" id="problems">
                        <strong>Answered problems</strong>
                        @include("problems.table", ['problems' => $unsolvedProblems])
                        <strong>Wrong answered problems</strong>
                        @include("problems.table", ['problems' => $solvedProblems])
                    </div>

                    <!-- User activity tab -->
                    <div role="tabpanel" class="fade tab-pane " id="userActivity">
                        {!! $chart->render() !!}
                    </div>

                    <!-- Contests tab -->
                    <div role="tabpanel" class="fade tab-pane " id="contests">

                        {{--<div class="content-tabs card">--}}
                        {{--<ul class="nav nav-tabs" role="tablist">--}}
                        {{--<li class=" nav-item active" role="presentation">--}}
                        {{--<a href="#part" role="tab" data-toggle="tab">Your Participated Contests</a>--}}
                        {{--</li>--}}
                        {{--<li class=" nav-item " role="presentation">--}}
                        {{--<a href="#owned" role="tab" data-toggle="tab">Owned Contests</a>--}}
                        {{--</li>--}}
                        {{--<li class=" nav-item " role="presentation">--}}
                        {{--<a href="#admin" role="tab" data-toggle="tab">Contests you are admin in</a>--}}
                        {{--</li>--}}
                        {{--</ul>--}}


                        {{--<div class="tab-content">--}}


                        {{--<!-- patricipated contests -->--}}
                        {{--<div role="tabpanel" class="fade in tab-pane active" id="part">--}}
                        {{--<div class="panel-body problems-panel-body">--}}
                        {{--@include('contests.contest_views.contests_table', ['contests' => $participatedContests, 'fragment' => ''])--}}
                        {{--</div>--}}
                        {{--</div>--}}


                        {{--<!-- owned contests -->--}}
                        {{--<div role="tabpanel" class="fade tab-pane" id="owned">--}}
                        {{--@include('contests.contest_views.contests_table', ['contests' => $owned, 'fragment' => ''])--}}
                        {{--</div>--}}


                        {{--<!-- admin in contests -->--}}
                        {{--<div role="tabpanel" class="fade tab-pane" id="admin">--}}
                        {{--@include('contests.contest_views.contests_table', ['contests' => $admin, 'fragment' => ''])--}}
                        {{--</div>--}}
                        {{--</div>--}}
                        {{--</div>--}}
                    </div>

                    <div role="tabpanel" class="fade tab-pane " id="groups">
                        <strong>Your groups</strong>
                        {{--@include('groups.groups_table')--}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
