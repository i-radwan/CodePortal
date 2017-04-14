{{--define some vars--}}
@php
    $ownerUsername = $data[Constants::SINGLE_CONTEST_CONTEST_KEY][Constants::SINGLE_CONTEST_OWNER_KEY];
    $isOwner = $data[Constants::SINGLE_CONTEST_EXTRA_KEY][Constants::SINGLE_CONTEST_IS_USER_OWNER];
    $isParticipant = $data[Constants::SINGLE_CONTEST_EXTRA_KEY][Constants::SINGLE_CONTEST_IS_USER_PARTICIPATING];
    $isOrganizer = $data[Constants::SINGLE_CONTEST_EXTRA_KEY][Constants::SINGLE_CONTEST_IS_USER_AN_ORGANIZER];
    $contestID = $data[Constants::SINGLE_CONTEST_CONTEST_KEY][Constants::SINGLE_CONTEST_ID_KEY];
    $contestName = $data[Constants::SINGLE_CONTEST_CONTEST_KEY][Constants::SINGLE_CONTEST_NAME_KEY];
    $contestTime = $data[Constants::SINGLE_CONTEST_CONTEST_KEY][Constants::SINGLE_CONTEST_TIME_KEY];
    $contestDuration = $data[Constants::SINGLE_CONTEST_CONTEST_KEY][Constants::SINGLE_CONTEST_DURATION_KEY];
    $contestOrganizers = $data[Constants::SINGLE_CONTEST_CONTEST_KEY][Constants::SINGLE_CONTEST_ORGANIZERS_KEY];
    $isContestRunning = $data[Constants::SINGLE_CONTEST_EXTRA_KEY][Constants::SINGLE_CONTEST_RUNNING_STATUS];
@endphp

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    {{--Contest leave/delete links--}}

                    @if($isOwner)
                        <a onclick="return confirm('Are you sure?')"
                           href="{{url('/contest/delete/'.$contestID)}}">
                            <span class="pull-right text-dark btn btn-link margin-5px">Delete</span>
                        </a>
                    @endif

                    @if($isParticipant)
                        <a onclick="return confirm('Are you sure?')"
                           href="{{url('/contest/leave/'.$contestID)}}">
                            <span class="pull-right text-dark btn btn-link margin-5px">Leave</span>
                        </a>

                    @elseif(Auth::user() && !$isOwner)
                        <a href="{{url('/contest/join/'.$contestID)}}">
                            <span class="pull-right text-dark btn btn-link margin-5px">Join</span>
                        </a>
                    @endif


                    {{--//Contest leave/delete links//--}}

                    <div class="panel-heading">
                        {{$contestName}}
                    </div>

                    <div class="panel-body">
                        {{--Alerts Part--}}
                        @if (count($errors) > 0)

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-danger">
                                        @foreach ($errors->all() as $error)
                                            <p>• {{ $error }}</p>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        @include('contests.contest_views.contest_info')

                        {{--Tabs Section--}}
                        <div class="row contest-tabs">
                            <div class="col-md-12">
                                <!-- Nav tabs -->
                                <div class="card">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li role="presentation" class="active">
                                            <a href="#problems" aria-controls="problems" role="tab" data-toggle="tab">Problems</a>
                                        </li>
                                        <li role="presentation">
                                            <a href="#standings" aria-controls="standings" role="tab" data-toggle="tab">Standings</a>
                                        </li>
                                        <li role="presentation">
                                            <a href="#status" aria-controls="status" role="tab"
                                               data-toggle="tab">Status</a>
                                        </li>
                                        <li role="presentation">
                                            <a href="#participants" aria-controls="participants" role="tab"
                                               data-toggle="tab">Participants</a>
                                        </li>
                                        <li role="presentation">
                                            <a href="#questions" aria-controls="questions" role="tab"
                                               data-toggle="tab">Questions</a>
                                        </li>
                                    </ul>

                                    <!-- Tab panes -->
                                    <div class="tab-content">
                                        <div role="tabpanel" class="tab-pane active" id="problems">
                                            Problems
                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="standings">
                                            Standings
                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="status">
                                            Status
                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="participants">
                                            @include('contests.contest_views.participants')
                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="questions">
                                            @if($data['questions'] && count($data['questions']))
                                                @include('contests.contest_views.questions')
                                            @else
                                                <p class="no-questions-msg">No questions!</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{--Show questions section when contest is running only and when user is participant--}}
                        @if($isContestRunning && $isParticipant)
                            @include('contests.contest_views.ask_question')
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @if ($isOrganizer)
            @include('contests.contest_views.answer_question_modal')
        @endif

    </div>
@endsection