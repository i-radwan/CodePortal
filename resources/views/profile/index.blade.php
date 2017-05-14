@php
    use App\Utilities\Constants;
    $isProfileOwner = (Auth::check() && Auth::user()[Constants::FLD_USERS_ID] == $user[Constants::FLD_USERS_ID]);
    $solvedProblems = $user->problems(true)->paginate(Constants::PROBLEMS_COUNT_PER_PAGE);
    $unsolvedProblems = $user->problems(false)->paginate(Constants::PROBLEMS_COUNT_PER_PAGE);
    $organizingContests = $user->organizingContests()->paginate(Constants::CONTESTS_COUNT_PER_PAGE);
    $owningContests = $user->owningContests()->paginate(Constants::CONTESTS_COUNT_PER_PAGE);
    $participatedContests = $user->participatingContests()->paginate(Constants::CONTESTS_COUNT_PER_PAGE);
    $joiningGroups = $user->joiningGroups()->paginate(Constants::GROUPS_COUNT_PER_PAGE);
    $administratingGroups = $user->administratingGroups()->paginate(Constants::GROUPS_COUNT_PER_PAGE);
    $owningGroups = $user->owningGroups()->paginate(Constants::GROUPS_COUNT_PER_PAGE);
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
                    <li class=" nav-item active" role="presentation">
                        <a href="#userInfo" role="tab" data-toggle="tab">Profile</a>
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

                <!-- Nav tabs contents -->
                <div class="tab-content col-sm-9">
                    <!-- User info tab -->
                    <div role="tabpanel" class="fade in tab-pane active" id="userInfo">
                        @include('profile.user_info')
                        {!! $chart->render() !!}
                    </div>

                    <!-- Problems tab -->
                    <div role="tabpanel" class="fade tab-pane" id="problems">
                        @include("problems.table", ['problems' => $solvedProblems])
                        @include("problems.table", ['problems' => $unsolvedProblems])
                    </div>

                    <!-- Contests tab -->
                    <div role="tabpanel" class="fade tab-pane " id="contests">
                        @include('profile.contest')
                    </div>

                    <!-- Groups tab -->
                    <div role="tabpanel" class="fade tab-pane " id="groups">
                        @include('groups.groups_table', ['groups' => $joiningGroups])
                    </div>

                    <!-- Teams tab -->
                    <div role="tabpanel" class="fade tab-pane " id="teams">
                        {{--TODO: add teams and split routes --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
