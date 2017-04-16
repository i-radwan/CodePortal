@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="panel panel-default contests-panel">
            <a href="{{ url('/contest/add') }}">
                <span class="btn btn-link text-dark pull-right margin-5px">New</span>
            </a>
            <div class="panel-heading contests-panel-heading">Contests</div>
            <div class="panel-body contests-panel-body horizontal-scroll">
                @include('contests.contest_views.contests_table')
            </div>
        </div>
    </div>
@endsection