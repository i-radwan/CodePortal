@php
    $sheetID = $data[\App\Utilities\Constants::SINGLE_SHEET_SHEET_KEY][\App\Utilities\Constants::SINGLE_SHEET_ID_KEY];
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
                {{--Alerts Part--}}
                @include('components.alert')
                @if(count($problems))
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center" width="60%">Name</th>
                            <th class="text-center">Solved</th>
                            <th class="text-center" width="20%">Solution</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($problems as $problem)
                            @php($verdict = $problem->simpleVerdict($user))
                            @php($solution = $problem->pivot->solution)
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

                                {{--Actions--}}
                                <td>
                                    <button class="btn btn-primary"
                                            data-toggle="modal"
                                            id="testing-solution-btn-problem-{{ $problem->id }}"
                                            data-target="#problem-solution-model"
                                            onclick="app.fillAnswerModal('{{$problem->id}}', '{{$sheetID}}', '{{url("sheet/solution/$sheetID/".$problem->id)}}', '{{$problem->pivot->solution_lang}}');">
                                        Solution
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="margin-30px">No problems!</p>
                @endif
            </div>
        </div>
        @include('groups.sheet_views.answer_problem_modal')
    </div>
    <span class="page-distinguishing-element" id="sheet-page-hidden-element"></span>
@endsection