@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="panel panel-default contests-panel">
            <a href="{{ url('/contest/add') }}">
                <span class="btn btn-link text-dark pull-right margin-5px">New</span>
            </a>
            <div class="panel-heading contests-panel-heading">Contests</div>
            <div class="panel-body contests-panel-body horizontal-scroll">
                @if(count($data))
                    <table class="table table-bordered" id="contests_table">
                        <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center contest-table-name-th">Name</th>
                            <th class="text-center">Time</th>
                            <th class="text-center">Duration</th>
                            <th class="text-center">Owner</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data[Constants::CONTESTS_CONTESTS_KEY] as $contest)
                            <tr>
                                <td>{{ $contest->id }}</td>
                                <td>
                                    <a href="{{ url('contest/' . $contest->id) }}">
                                        {{ $contest->name }}
                                    </a>
                                </td>
                                {{--TODO: use Carbon human readable date/time formats--}}
                                <td>{{ date('D M d, H:i', strtotime($contest->time))}}</td>
                                {{--TODO: use Carbon human readable date/time formats--}}
                                <td>{{ \App\Utilities\Utilities::convertMinsToHoursMins($contest->duration) }} hrs</td>
                                <td>
                                    <a href="{{ url('profile/' . $contest->owner->username)}}">
                                        {{ $contest->owner->username }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{--Pagination--}}
                    {{ $data[Constants::CONTESTS_CONTESTS_KEY]->render() }}
                @else
                    <p class="no-contests-msg">No contests!</p>
                @endif
            </div>
        </div>
    </div>
@endsection