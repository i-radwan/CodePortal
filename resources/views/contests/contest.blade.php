{{--define some variables--}}
@php
    $contestID = $contestInfo[Constants::SINGLE_CONTEST_ID_KEY];
    $contestName = $contestInfo[Constants::SINGLE_CONTEST_NAME_KEY];
    $contestTime = $contestInfo[Constants::SINGLE_CONTEST_TIME_KEY];
    $contestDuration = $contestInfo[Constants::SINGLE_CONTEST_DURATION_KEY];
    $contestOrganizers = $contestInfo[Constants::SINGLE_CONTEST_ORGANIZERS_KEY];
    $isContestRunning = $contestInfo[Constants::SINGLE_CONTEST_RUNNING_STATUS];

    $ownerUsername = $contestInfo[Constants::SINGLE_CONTEST_OWNER_KEY];
    $isOwnerOrOrganizer = Gate::allows('owner-organizer-contest', $contestID);

@endphp

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="panel panel-default">

            {{--Contest leave/delete/reorder/join links--}}
            @if($isOwner)

                {{-- Delete Form --}}
                @include('components.action_form', ['url' => url('contest/delete/'.$contestID), 'method' => 'DELETE', 'confirm' => true, 'confirmMsg' => "'Are you sure want to delete this contest? This action cannot be undone!'", 'btnIDs' => '', 'btnClasses' => 'btn btn-link text-dark pull-right margin-5px', 'btnTxt' => 'Delete'])

                {{-- Edit Form --}}
                <a href="{{url('contest/'.$contestID.'/edit')}}" class="btn btn-link text-dark pull-right margin-5px">Edit</a>

                {{-- Reorder Contest Problems --}}
                <span class="btn btn-link text-dark pull-right margin-5px"
                      onclick="app.toggleSortableStatus();" id="testing-reorder-btn">Reorder</span>

            @endif
            @if($isParticipant)

                {{--Leave Form--}}
                @include('components.action_form', ['url' => url('contest/leave/'.$contestID), 'method' => 'PUT', 'confirm' => true, 'confirmMsg' => "'Are you sure want to leave this contest?'", 'btnIDs' => 'testing-contest-leave-btn', 'btnClasses' => 'btn btn-link text-dark pull-right margin-5px', 'btnTxt' => 'Leave'])

            @elseif(Auth::check())

                {{--Join Form--}}
                @include('components.action_form', ['url' => url('contest/join/'.$contestID), 'method' => 'POST', 'confirm' => false, 'btnClasses' => 'btn btn-link text-dark pull-right margin-5px', 'btnIDs' => 'testing-contest-join-btn', 'btnTxt' => 'Join'])

            @endif

            <div class="panel-heading">{{ $contestName }}</div>

            <div class="panel-body">
                {{--Alerts Part--}}
                @include('components.alert')

                {{--Contest info: time, duration, owner, etc.--}}
                @include('contests.contest_views.contest_info')

                {{--Tabs Section--}}
                <div class="content-tabs card">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li {{($view == "problems")?'class=active':''}}>
                            <a href="{{ url("/contest/$contestID/problems") }}">Problems</a>
                        </li>
                        <li {{($view == "standings")?'class=active':''}}>
                            <a href="{{ url("/contest/$contestID/standings") }}">Standings</a>
                        </li>
                        <li {{($view == "submissions")?'class=active':''}}>
                            <a href="{{ url("/contest/$contestID/status") }}">Status</a>
                        </li>
                        <li {{($view == "participants")?'class=active':''}}>
                            <a href="{{ url("/contest/$contestID/participants") }}">Participants</a>
                        </li>
                        <li {{($view == "questions")?'class=active':''}}>
                            <a href="{{ url("/contest/$contestID/questions") }}">Questions</a>
                        </li>
                    </ul>

                    {{--Contest specific view--}}
                    <div class="tab-content text-center">
                        <div role="tabpanel" class="tab-pane active horizontal-scroll">

                            {{--Problems--}}
                            @if($view == "problems")
                                @if($problems && count($problems))
                                    <button
                                            type="submit" class="btn btn-primary pull-right problems-reorder-view save"
                                            onclick="app.saveProblemsOrderToDB('{{url('contest/reorder/'.$contestID)}}', '{{csrf_token()}}')">
                                        Save
                                    </button>
                                    @include('contests.contest_views.problems')
                                @else
                                    @if(!$isContestRunning && !$isContestEnded)
                                        <p>Problems will be visible when the contest begins!</p>
                                    @else
                                        <p>No problems!</p>
                                    @endif
                                @endif

                                {{--Standings--}}
                            @elseif($view == "standings")
                                @if(!$isContestRunning && !$isContestEnded)
                                    <p>Standings will be visible when the contest begins!</p>
                                @else
                                    @include('contests.contest_views.standings')
                                @endif

                                {{--Status--}}
                            @elseif($view == "submissions")
                                @if($submissions && count($submissions))
                                    @include('contests.contest_views.status')
                                @else
                                    <p>No submissions!</p>
                                @endif

                                {{--Participants--}}
                            @elseif($view == "participants")
                                @if($participants && count($participants))
                                    @include('contests.contest_views.participants')
                                @else
                                    <p>No participants!</p>
                                @endif

                                {{--Questions--}}
                            @elseif($view == "questions")
                                @if($questions && count($questions))
                                    @include('contests.contest_views.questions')
                                @else
                                    <p>No questions!</p>
                                @endif
                            @endif

                        </div>
                    </div>
                </div>

                {{--Show questions section when contest is running only and when user is participant--}}
                @if($isContestRunning && $isParticipant)
                    @include('contests.contest_views.ask_question')
                @endif
            </div>
        </div>

        @if (Gate::allows('owner-organizer-contest', $contestID))
            @include('contests.contest_views.answer_question_modal')
        @endif

    </div>
    <span class="page-distinguishing-element" id="single-contest-page-hidden-element"></span>

@endsection
