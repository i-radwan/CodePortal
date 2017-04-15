{{--define some variables--}}
@php
    $groupID = $data[Constants::SINGLE_GROUP_GROUP_KEY][Constants::SINGLE_GROUP_ID_KEY];
    $groupName = $data[Constants::SINGLE_GROUP_GROUP_KEY][Constants::SINGLE_GROUP_NAME_KEY];
    $ownerUsername = $data[Constants::SINGLE_GROUP_GROUP_KEY][Constants::SINGLE_GROUP_OWNER_KEY];
    //$contestTime = $data[Constants::SINGLE_CONTEST_CONTEST_KEY][Constants::SINGLE_CONTEST_TIME_KEY];
    //$contestDuration = $data[Constants::SINGLE_CONTEST_CONTEST_KEY][Constants::SINGLE_CONTEST_DURATION_KEY];
    //$contestOrganizers = $data[Constants::SINGLE_CONTEST_CONTEST_KEY][Constants::SINGLE_CONTEST_ORGANIZERS_KEY];
    //$isContestRunning = $data[Constants::SINGLE_CONTEST_EXTRA_KEY][Constants::SINGLE_CONTEST_RUNNING_STATUS];

    //$ownerUsername = $data[Constants::SINGLE_CONTEST_CONTEST_KEY][Constants::SINGLE_CONTEST_OWNER_KEY];
    $isOwner = $data[Constants::SINGLE_GROUP_EXTRA_KEY][Constants::SINGLE_GROUP_IS_USER_OWNER];
    $userSentRequest = $data[Constants::SINGLE_GROUP_EXTRA_KEY][Constants::SINGLE_GROUP_USER_SENT_REQUEST];
    $isMember = $data[Constants::SINGLE_GROUP_EXTRA_KEY][Constants::SINGLE_GROUP_IS_USER_MEMBER];

    //$problems = $data[Constants::SINGLE_CONTEST_PROBLEMS_KEY];
    //$standings = $data[Constants::SINGLE_CONTEST_STANDINGS_KEY];
    //$status = $data[Constants::SINGLE_CONTEST_STATUS_KEY];
    //$participants = $data[Constants::SINGLE_CONTEST_PARTICIPANTS_KEY];
    //$questions = $data[Constants::SINGLE_CONTEST_QUESTIONS_KEY];
@endphp

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="panel panel-default">

            {{--Contest leave/delete/join links--}}
            @if($isOwner)
                <form action="{{url('group/'.$groupID)}}"
                      method="post">{{method_field('DELETE')}}
                    {{csrf_field()}}
                    <button
                            onclick="return confirm('Are you sure want to delete the group?\nThis cannot be undone!')"
                            type="submit" class="btn btn-link text-dark pull-right margin-5px">Delete
                    </button>
                </form>
            @endif
            @if($isMember)
                <form action="{{url('group/leave/'.$groupID)}}"
                      method="post">{{method_field('PUT')}}
                    {{csrf_field()}}
                    <button
                            onclick="return confirm('Are you sure want to leave the group?')"
                            type="submit" class="btn btn-link text-dark pull-right margin-5px">Leave
                    </button>
                </form>
            @elseif(!$isOwner && !$isMember && !$userSentRequest)
                <form action="{{url('group/join/'.$groupID)}}"
                      method="post">{{method_field('POST')}}
                    {{csrf_field()}}
                    <button type="submit" class="btn btn-link text-dark pull-right margin-5px">Join
                    </button>
                </form>
            @endif

            <div class="panel-heading">{{ $groupName }} ::
                <small><a href="{{url('profile/'.$ownerUsername)}}">{{$ownerUsername}}</a></small>
            </div>

            {{--<div class="panel-body">--}}
            {{--Alerts Part--}}
            {{--@if(count($errors) > 0)--}}
            {{--<div class="alert alert-danger">--}}
            {{--<ul>--}}
            {{--@foreach ($errors->all() as $error)--}}
            {{--<li>{{ $error }}</li>--}}
            {{--@endforeach--}}
            {{--</ul>--}}
            {{--</div>--}}
            {{--@endif--}}

            {{--Contest info: time, duration, owner, etc.--}}
            {{--@include('contests.contest_views.contest_info')--}}

            {{--Tabs Section--}}
            {{--<div class="contest-tabs card">--}}
            {{--<!-- Nav tabs -->--}}
            {{--<ul class="nav nav-tabs" role="tablist">--}}
            {{--<li role="presentation" class="active">--}}
            {{--<a href="#problems" aria-controls="problems" role="tab" data-toggle="tab">Problems</a>--}}
            {{--</li>--}}
            {{--<li role="presentation">--}}
            {{--<a href="#standings" aria-controls="standings" role="tab" data-toggle="tab">Standings</a>--}}
            {{--</li>--}}
            {{--<li role="presentation">--}}
            {{--<a href="#status" aria-controls="status" role="tab" data-toggle="tab">Status</a>--}}
            {{--</li>--}}
            {{--<li role="presentation">--}}
            {{--<a href="#participants" aria-controls="participants" role="tab" data-toggle="tab">Participants</a>--}}
            {{--</li>--}}
            {{--<li role="presentation">--}}
            {{--<a href="#questions" aria-controls="questions" role="tab" data-toggle="tab">Questions</a>--}}
            {{--</li>--}}
            {{--</ul>--}}

            {{--<!-- Tab panes -->--}}
            {{--<div class="tab-content">--}}
            {{--<div role="tabpanel" class="tab-pane active" id="problems">--}}
            {{--@include('contests.contest_views.problems')--}}
            {{--</div>--}}
            {{--<div role="tabpanel" class="tab-pane" id="standings">--}}
            {{--@include('contests.contest_views.standings')--}}
            {{--</div>--}}
            {{--<div role="tabpanel" class="tab-pane" id="status">--}}
            {{--@include('contests.contest_views.status')--}}
            {{--</div>--}}
            {{--<div role="tabpanel" class="tab-pane" id="participants">--}}
            {{--@include('contests.contest_views.participants')--}}
            {{--</div>--}}
            {{--<div role="tabpanel" class="tab-pane  horizontal-scroll" id="questions">--}}
            {{--@if($questions && count($questions))--}}
            {{--@include('contests.contest_views.questions')--}}
            {{--@else--}}
            {{--<p class="no-questions-msg">No questions!</p>--}}
            {{--@endif--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}

            {{--Show questions section when contest is running only and when user is participant--}}
            {{--@if($isContestRunning && $isParticipant)--}}
            {{--@include('contests.contest_views.ask_question')--}}
            {{--@endif--}}
            {{--</div>--}}
        </div>
    </div>
@endsection