{{--Define some variables--}}
@php
    use \App\Utilities\Constants;
    use \App\Utilities\Utilities;

    $contestID = $contest[Constants::FLD_CONTESTS_ID];
    $contestName = $contest[Constants::FLD_CONTESTS_NAME];
    $contestDateTime = strtotime($contest[Constants::FLD_CONTESTS_TIME]);
    $contestTime = date('D M d, H:i', $contestDateTime);
    $contestYear = date('Y', $contestDateTime);
    $contestMonth = date('m', $contestDateTime);
    $contestDay = date('d', $contestDateTime);
    $contestHour = date('H', $contestDateTime);
    $contestMinute = date('i', $contestDateTime);

    $contestDuration = Utilities::convertSecondsToDaysHoursMins($contest[Constants::FLD_CONTESTS_DURATION]);
    $contestOrganizers = $contest->organizers()->pluck(Constants::FLD_USERS_USERNAME);

    $ownerUsername = $contest->owner[Constants::FLD_USERS_USERNAME];
    $isOwnerOrOrganizer = $isOwner || $isUserOrganizer;
@endphp

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="panel panel-default">

            {{--Contest leave/delete/reorder/join links--}}
            @if($isOwner)

                {{--Delete Form--}}
                @include('components.action_form', [
                    'url' => route(\App\Utilities\Constants::ROUTES_CONTESTS_DELETE, $contestID),
                    'method' => 'DELETE',
                    'confirm' => true,
                    'confirmMsg' => "'Are you sure want to delete this contest? This action cannot be undone!'",
                    'btnIDs' => '',
                    'btnClasses' => 'btn btn-link text-dark pull-right margin-5px',
                    'btnTxt' => 'Delete'
                ])

                {{--Edit Form--}}
                <a href="{{ route(\App\Utilities\Constants::ROUTES_CONTESTS_EDIT, $contestID) }}"
                   class="btn btn-link text-dark pull-right margin-5px">
                    Edit
                </a>

                {{--Reorder Contest Problems--}}
                <span class="btn btn-link text-dark pull-right margin-5px"
                      onclick="app.toggleSortableStatus();"
                      id="testing-reorder-btn">
                    Reorder
                </span>
            @endif

            @if($isParticipant)
                {{--Leave Form--}}
                @include('components.action_form', [
                    'url' => route(\App\Utilities\Constants::ROUTES_CONTESTS_LEAVE, $contestID),
                    'method' => 'PUT',
                    'confirm' => true,
                    'confirmMsg' => "'Are you sure want to leave this contest?'",
                    'btnIDs' => 'testing-contest-leave-btn',
                    'btnClasses' => 'btn btn-link text-dark pull-right margin-5px',
                    'btnTxt' => 'Leave'
                ])
            @elseif(!$isContestEnded)
                {{--Join Form--}}
                @include('components.action_form', [
                    'url' => route(\App\Utilities\Constants::ROUTES_CONTESTS_JOIN, $contestID),
                    'method' => 'POST',
                    'confirm' => false,
                    'btnClasses' => 'btn btn-link text-dark pull-right margin-5px',
                    'btnIDs' => 'testing-contest-join-btn',
                    'btnTxt' => 'Join'
                ])
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
                        <li {{ ($view == "problems") ? 'class=active' : '' }}>
                            <a href="{{ route(\App\Utilities\Constants::ROUTES_CONTESTS_PROBLEMS, $contestID) }}">
                                Problems
                            </a>
                        </li>
                        <li {{ ($view == "standings") ? 'class=active' : '' }}>
                            <a href="{{ route(\App\Utilities\Constants::ROUTES_CONTESTS_STANDINGS, $contestID) }}">
                                Standings
                            </a>
                        </li>
                        <li {{ ($view == "submissions") ? 'class=active' : '' }}>
                            <a href="{{ route(\App\Utilities\Constants::ROUTES_CONTESTS_STATUS, $contestID) }}">
                                Status
                            </a>
                        </li>
                        <li {{ ($view == "participants") ? 'class=active' : '' }}>
                            <a href="{{ route(\App\Utilities\Constants::ROUTES_CONTESTS_PARTICIPANTS, $contestID) }}">
                                Participants
                            </a>
                        </li>
                        <li {{ ($view == "questions") ? 'class=active' : '' }}>
                            <a href="{{ route(\App\Utilities\Constants::ROUTES_CONTESTS_QUESTIONS, $contestID) }}">
                                Questions
                            </a>
                        </li>
                    </ul>

                    {{--Contest specific view--}}
                    <div class="tab-content text-center">
                        <div role="tabpanel" class="tab-pane active horizontal-scroll">

                            {{--Problems--}}
                            @if($view == "problems")
                                @if(!$isContestRunning && !$isContestEnded && !$isOwnerOrOrganizer)
                                    <p>Problems will be visible when the contest begins!</p>
                                @elseif($problems && count($problems))
                                    <button type="submit"
                                            class="btn btn-primary pull-right problems-reorder-view save"
                                            onclick="app.saveProblemsOrderToDB('{{ route(\App\Utilities\Constants::ROUTES_CONTESTS_REORDER, $contestID) }}', '{{csrf_token()}}')">
                                        Save
                                    </button>
                                    @include('contests.contest_views.problems')
                                @else
                                    <p>No problems!</p>
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

                                {{--Show questions section when contest is running only and when user is participant--}}
                                @if($isContestRunning && $isParticipant)
                                    @include('contests.contest_views.ask_question')
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($isOwnerOrOrganizer)
            @include('contests.contest_views.answer_question_modal')
        @endif
    </div>

    <span class="page-distinguishing-element" id="single-contest-page-hidden-element"></span>
@endsection
