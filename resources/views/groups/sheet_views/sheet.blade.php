@php
    $sheetName = $data[\App\Utilities\Constants::SINGLE_SHEET_SHEET_KEY][\App\Utilities\Constants::SINGLE_SHEET_NAME_KEY];
    $problems = $data[\App\Utilities\Constants::SINGLE_SHEET_PROBLEMS_KEY];
    $isOwner = $data[\App\Utilities\Constants::SINGLE_SHEET_EXTRA_KEY][\App\Utilities\Constants::SINGLE_GROUP_IS_USER_OWNER];
    $user = Auth::user();
@endphp
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="panel panel-default groups-panel">
            <div class="panel-heading groups-panel-heading">{{$sheetName}} - Problems</div>
            <div class="panel-body groups-panel-body horizontal-scroll">
                @if(count($problems))
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center" width="60%">Name</th>
                            <th class="text-center">Solved</th>
                            @if($isOwner)
                                <th class="text-center" width="20%">Actions</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($problems as $problem)
                            @php($verdict = $problem->simpleVerdict($user))
                            <tr class="{{ $verdict == Constants::SIMPLE_VERDICT_ACCEPTED ? 'success' : ($verdict == Constants::SIMPLE_VERDICT_WRONG_SUBMISSION ? 'danger' : '') }}">
                                {{--ID--}}
                                <td>{{ Utilities::generateProblemNumber($problem) }}</td>

                                {{--Name--}}
                                <td>
                                    <a href="{{ Utilities::generateProblemLink($problem) }}" target="_blank">
                                        {{ $problem->name }}
                                    </a>
                                </td>

                                {{--Solved count--}}
                                <td>{{ $problem->solved_count }}</td>

                                @if($isOwner)
                                    {{--Actions--}}
                                    <td>
                                        <button class="btn btn-primary">
                                            Answer
                                        </button>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="margin-30px">No problems!</p>
                @endif
            </div>
        </div>
    </div>
@endsection