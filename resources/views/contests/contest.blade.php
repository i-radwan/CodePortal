{{--define some variables--}}
@php
    $contestID = $data[Constants::SINGLE_CONTEST_CONTEST_KEY][Constants::SINGLE_CONTEST_ID_KEY];
    $contestName = $data[Constants::SINGLE_CONTEST_CONTEST_KEY][Constants::SINGLE_CONTEST_NAME_KEY];
    $contestTime = $data[Constants::SINGLE_CONTEST_CONTEST_KEY][Constants::SINGLE_CONTEST_TIME_KEY];
    $contestDuration = $data[Constants::SINGLE_CONTEST_CONTEST_KEY][Constants::SINGLE_CONTEST_DURATION_KEY];
    $contestOrganizers = $data[Constants::SINGLE_CONTEST_CONTEST_KEY][Constants::SINGLE_CONTEST_ORGANIZERS_KEY];
    $isContestRunning = $data[Constants::SINGLE_CONTEST_EXTRA_KEY][Constants::SINGLE_CONTEST_RUNNING_STATUS];

    $ownerUsername = $data[Constants::SINGLE_CONTEST_CONTEST_KEY][Constants::SINGLE_CONTEST_OWNER_KEY];
    $isOwner = $data[Constants::SINGLE_CONTEST_EXTRA_KEY][Constants::SINGLE_CONTEST_IS_USER_OWNER];
    $isOwnerOrOrganizer = Gate::allows('owner-organizer-contest', $contestID);
    $isParticipant = $data[Constants::SINGLE_CONTEST_EXTRA_KEY][Constants::SINGLE_CONTEST_IS_USER_PARTICIPATING];

    $problems = $data[Constants::SINGLE_CONTEST_PROBLEMS_KEY];
    $standings = $data[Constants::SINGLE_CONTEST_STANDINGS_KEY];
    $status = $data[Constants::SINGLE_CONTEST_STATUS_KEY];
    $participants = $data[Constants::SINGLE_CONTEST_PARTICIPANTS_KEY];
    $questions = $data[Constants::SINGLE_CONTEST_QUESTIONS_KEY];
@endphp

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="panel panel-default">

            {{--Contest leave/delete/join links--}}
            @if($isOwner)
                <a onclick="return confirm('Are you sure want to delete the contest?\nThis cannot be undone')"
                   href="{{ url('/contest/delete/' . $contestID) }}">
                    <span class="btn btn-link text-dark pull-right margin-5px">Delete</span>
                </a>
            @endif
            @if($isParticipant)
                <a onclick="return confirm('Are you sure want to leave the contest?')"
                   href="{{ url('/contest/leave/' . $contestID) }}">
                    <span class="btn btn-link text-dark pull-right margin-5px">Leave</span>
                </a>
            @elseif(Auth::check())
                <a href="{{ url('/contest/join/' . $contestID) }}">
                    <span class="btn btn-link text-dark pull-right margin-5px">Join</span>
                </a>
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
                        <li role="presentation" class="active">
                            <a href="#problems" aria-controls="problems" role="tab" data-toggle="tab">Problems</a>
                        </li>
                        <li role="presentation">
                            <a href="#standings" aria-controls="standings" role="tab" data-toggle="tab">Standings</a>
                        </li>
                        <li role="presentation">
                            <a href="#status" aria-controls="status" role="tab" data-toggle="tab">Status</a>
                        </li>
                        <li role="presentation">
                            <a href="#participants" aria-controls="participants" role="tab" data-toggle="tab">Participants</a>
                        </li>
                        <li role="presentation">
                            <a href="#questions" aria-controls="questions" role="tab" data-toggle="tab">Questions</a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content text-center">
                        <div role="tabpanel" class="tab-pane active" id="problems">
                            @include('contests.contest_views.problems')
                        </div>
                        <div role="tabpanel" class="tab-pane" id="standings">
                            @include('contests.contest_views.standings')
                        </div>
                        <div role="tabpanel" class="tab-pane" id="status">
                            @include('contests.contest_views.status')
                        </div>
                        <div role="tabpanel" class="tab-pane" id="participants">
                            @include('contests.contest_views.participants')
                        </div>
                        <div role="tabpanel" class="tab-pane horizontal-scroll" id="questions">
                            @if($questions && count($questions))
                                @include('contests.contest_views.questions')
                            @else
                                <h3>No questions!</h3>
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
@endsection
