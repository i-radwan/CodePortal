{{--Display single contest problems info--}}
<table class="table table-bordered table-hover text-center">
    <thead>
        <tr>
            <th class="text-center">Rank</th>
            <th class="text-center">Contestant</th>
            <th class="text-center">Solved</th>
            <th class="text-center">Penalty</th>

            @foreach($problems as $problem)
                <th class="text-center">P{{ $loop->index }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($standings as $row)
            <tr>
                {{--TODO: get rank from database--}}
                <td>{{ $loop->index + 1 }}</td>
                <td>{{ $row[\App\Utilities\Constants::FLD_USERS_USERNAME] }}</td>
                <td>{{ $row[\App\Utilities\Constants::FLD_USERS_SOLVED_COUNT] }} / {{ $row[\App\Utilities\Constants::FLD_USERS_TRAILS_COUNT] }}</td>
                <td>{{ $row[\App\Utilities\Constants::FLD_USERS_PENALTY] }}</td>

                @foreach($row[\App\Utilities\Constants::TBL_PROBLEMS] as $problem)
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