@php
    use \App\Utilities\Constants;

    $sheetID = $sheet[Constants::SINGLE_SHEET_ID_KEY];
    $sheetName = $sheet[Constants::SINGLE_SHEET_NAME_KEY];
    $user = Auth::user();
    $isOwner = ($user) ? ($user->owningGroups()->find($sheet[Constants::FLD_SHEETS_GROUP_ID]) != null) : false;

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
                            @php
                                $verdict = $problem->simpleVerdict($user);
                                $solution = $problem->pivot[\App\Utilities\Constants::FLD_SHEET_PROBLEMS_SOLUTION];
                                $solutionLang = $problem->pivot[\App\Utilities\Constants::FLD_SHEET_PROBLEMS_SOLUTION_LANG];
                                $problemID = $problem[\App\Utilities\Constants::FLD_PROBLEMS_ID];
                                $problemName = $problem[\App\Utilities\Constants::FLD_PROBLEMS_NAME];
                                $problemNumber = \App\Utilities\Utilities::generateProblemNumber($problem);
                                $problemLink =\App\Utilities\Utilities::generateProblemLink($problem);
                                $problemSolvedCount = $problem[\App\Utilities\Constants::FLD_PROBLEMS_SOLVED_COUNT];
                            @endphp
                            <tr class="{{ $verdict == \App\Utilities\Constants::SIMPLE_VERDICT_ACCEPTED ? 'success' : ($verdict == \App\Utilities\Constants::SIMPLE_VERDICT_WRONG_SUBMISSION ? 'danger' : '') }}">

                                {{--ID--}}
                                <td>{{ $problemNumber }}</td>

                                {{--Name--}}
                                <td>
                                    <a href="{{ $problemLink }}"
                                       target="_blank">
                                        {{ $problemName }}
                                    </a>
                                </td>

                                {{--Solved count--}}
                                <td>{{ $problemSolvedCount }}</td>

                                {{--Actions--}}
                                <td>
                                    <button class="btn btn-primary"
                                            data-toggle="modal"
                                            id="testing-solution-btn-problem-{{ $problemID }}"
                                            data-target="#problem-solution-model"
                                            onclick="app.fillAnswerModal('{{ $problemID }}', '{{ $sheetID }}', '{{url("sheet/solution/$sheetID/" . $problemID)}}', '{{ $solutionLang }}');">
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