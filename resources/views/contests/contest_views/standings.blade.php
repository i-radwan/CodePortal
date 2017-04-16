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
                <td>{{ $loop->index }}</td>
                <td>{{ $row[\App\Utilities\Constants::FLD_USERS_USERNAME] }}</td>
                <td>{{ $row[\App\Utilities\Constants::FLD_USERS_SOLVED_COUNT] }}</td>
                <td>{{ $row[\App\Utilities\Constants::FLD_USERS_PENALTY] }}</td>

                @foreach($row[\App\Utilities\Constants::TBL_PROBLEMS] as $problem)
                    <td>
                        {{ $problem[\App\Utilities\Constants::FLD_PROBLEMS_TRAILS_COUNT] }}
                    </td>
                @endforeach
            </tr>
        @endforeach

        <tr>
            <td colspan="4">Number of Submissions</td>
        </tr>
        <tr>
            <td colspan="4">Number of Accepted Submissions</td>
        </tr>
    </tbody>
</table>
