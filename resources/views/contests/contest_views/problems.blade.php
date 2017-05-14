{{--Display single contest problems info--}}
<table class="table table-bordered table-hover text-center">
    <thead>
    <tr>
        <th class="text-center problems-reorder-view">#</th>
        <th class="text-center">ID</th>
        <th class="text-center">Name</th>
        <th class="text-center">Solved</th>
        <th class="text-center">Judge</th>
    </tr>
    </thead>

    <tbody id="contest-problems-tbody">
    @foreach($problems as $problem)
        @php
            $problem = (array)$problem;
            $trailsCount = $problem[\App\Utilities\Constants::FLD_PROBLEMS_TRAILS_COUNT];
            $problem = new \App\Models\Problem((array)$problem);
            $verdict = $problem->simpleVerdict($authUser);
            $problemRawID = $problem[\App\Utilities\Constants::FLD_PROBLEMS_ID];
            $problemName = $problem[\App\Utilities\Constants::FLD_PROBLEMS_NAME];
            $problemSolvedCount = $problem[\App\Utilities\Constants::FLD_PROBLEMS_SOLVED_COUNT];
            $id = \App\Utilities\Utilities::generateProblemNumber($problem);
            $link = \App\Utilities\Utilities::generateProblemLink($problem);
            $judgeData = \App\Utilities\Constants::JUDGES[$problem[\App\Utilities\Constants::FLD_PROBLEMS_JUDGE_ID]];
            $judgeLink = $judgeData[\App\Utilities\Constants::JUDGE_LINK_KEY];
            $judgeName = $judgeData[\App\Utilities\Constants::JUDGE_NAME_KEY];

            if ($verdict == \App\Utilities\Constants::SIMPLE_VERDICT_ACCEPTED)
                $style = 'success';
            elseif ($verdict == \App\Utilities\Constants::SIMPLE_VERDICT_WRONG_SUBMISSION)
                $style = 'danger';
            else
                $style = '';
        @endphp

        <tr class="{{ $style }}">
            {{--Reorder view--}}
            <td class="problems-reorder-view index" data-problem-id="{{ $problemRawID }}">
                <i class="fa fa-bars" id="testing-drag-problem-{{ $problemRawID }}" aria-hidden="true"></i>
            </td>

            {{--ID--}}
            <td class="testing-problem-order-{{ $loop->iteration }}">{{ $id }}</td>

            {{--Name--}}
            <td><a href="{{ $link }}" target="_blank">{{ $problemName }}</a></td>

            {{--Solved Count--}}
            <td>{{ $problemSolvedCount }} / {{ $trailsCount }}</td>

            {{--Judge--}}
            <td><a href="{{ $judgeLink }}" target="_blank">{{ $judgeName }}</a></td>
        </tr>
    @endforeach
    </tbody>
</table>
