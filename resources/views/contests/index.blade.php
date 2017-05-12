@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="panel panel-default">
            <a href="{{ route(\App\Utilities\Constants::ROUTES_CONTESTS_CREATE) }}">
                <span class="btn btn-link text-dark pull-right margin-5px">New</span>
            </a>

            <div class="panel-heading">Contests</div>

            <div class="panel-body">

                {{--Contests Tabs Section--}}
                <div class="content-tabs card">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#running" aria-controls="running" role="tab" data-toggle="tab">Running</a>
                        </li>
                        <li role="presentation">
                            <a href="#upcoming" aria-controls="upcoming" role="tab" data-toggle="tab">Upcoming</a>
                        </li>
                        <li role="presentation">
                            <a href="#past" aria-controls="past" role="tab" data-toggle="tab">Past</a>
                        </li>
                    </ul>

                    <!-- Contests Tab panes -->
                    <div class="tab-content text-center">

                        {{--Running Contests--}}
                        <div role="tabpanel" class="tab-pane active" id="running">
                            @include('contests.contest_views.contests_table', ['contests' => $runningContests, 'fragment' => 'running'])
                        </div>

                        {{--Upcoming Contests--}}
                        <div role="tabpanel" class="tab-pane" id="upcoming">
                            @include('contests.contest_views.contests_table', ['contests' => $upcomingContests, 'fragment' => 'upcoming'])
                        </div>

                        {{--Past Contests--}}
                        <div role="tabpanel" class="tab-pane" id="past">
                            @include('contests.contest_views.contests_table', ['contests' => $endedContests, 'fragment' => 'past'])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <span class="page-distinguishing-element" id="contests-page-hidden-element"></span>
@endsection