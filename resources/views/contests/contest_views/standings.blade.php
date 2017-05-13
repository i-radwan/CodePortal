{{--Display single contest problems info--}}
<table class="table table-bordered table-hover text-center">
    <thead>
    <tr>
        <th class="text-center">Rank</th>
        <th class="text-center">Contestant</th>
        <th class="text-center">Solved</th>
        <th class="text-center">Penalty</th>

        @foreach($problems as $problem)
            @php
                $problem = new \App\Models\Problem((array)(array)$problem);
                $id = \App\Utilities\Utilities::generateProblemNumber($problem);
                $link = \App\Utilities\Utilities::generateProblemLink($problem);
                $problemName = $problem[\App\Utilities\Constants::FLD_PROBLEMS_NAME];
            @endphp

            <th class="text-center">
                <a href="{{ $link }}" title="{{ $id . ' - ' . $problemName }}">
                    P{{ $loop->iteration }}
                </a>
            </th>
        @endforeach
    </tr>
    </thead>

    <tbody>
    @foreach($standings as $row)
        @php
            $username = $row[\App\Utilities\Constants::FLD_USERS_USERNAME];
            $solvedCount = $row[\App\Utilities\Constants::FLD_USERS_SOLVED_COUNT];
            $trailsCount = $row[\App\Utilities\Constants::FLD_USERS_TRAILS_COUNT];
            $penalty = $row[\App\Utilities\Constants::FLD_USERS_PENALTY];
            $userProblems = $row[\App\Utilities\Constants::TBL_PROBLEMS];
        @endphp
        <tr>
            {{--TODO: get rank from database--}}
            <td>{{ $loop->iteration }}</td>
            <td>{{ $username }}</td>
            <td>{{ $solvedCount }} / {{ $trailsCount }}</td>
            <td>{{ $penalty }}</td>

            @foreach($userProblems as $problem)
                @php
                    $trialsCount = $problem[\App\Utilities\Constants::FLD_PROBLEMS_TRAILS_COUNT];
                    $solvedCount = $problem[\App\Utilities\Constants::FLD_PROBLEMS_SOLVED_COUNT];

                    if ($solvedCount > 0) {
                        $style = 'success';
                        $char = '+';
                    }
                    elseif($trialsCount > 0) {
                        $style = 'danger';
                        $char = '-';
                    }
                    else {
                        $style = '';
                        $char = '';
                    }
                @endphp

                <td class="{{ $style }}">{{ $char . $trialsCount }}</td>
            @endforeach
        </tr>
    @endforeach

    <tr>
        <td colspan="4">Number of Submissions</td>
        @foreach($problems as $problem)
            <td>{{ ((array)$problem)[\App\Utilities\Constants::FLD_PROBLEMS_TRAILS_COUNT] }}</td>
        @endforeach
    </tr>

    <tr>
        <td colspan="4">Number of Accepted Submissions</td>
        @foreach($problems as $problem)
            <td>{{ ((array)$problem)[\App\Utilities\Constants::FLD_PROBLEMS_SOLVED_COUNT] }}</td>
        @endforeach
    </tr>
    </tbody>
</table>

{{--Pagination--}}
{{--{{ $standings->appends(Request::all())->fragment('standings')->render() }}--}}