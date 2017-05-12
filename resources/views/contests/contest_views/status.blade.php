{{--Display single contest problems info--}}
<table class="table table-bordered table-hover text-center">
    <thead>
        <tr>
            <th class="text-center">ID</th>
            <th class="text-center">Contestant</th>
            <th class="text-center">Problem</th>
            <th class="text-center">Judge</th>
            <th class="text-center">Verdict</th>
            <th class="text-center">Time</th>
            <th class="text-center">Memory</th>
            <th class="text-center">Language</th>
            <th class="text-center">Submitted At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($submissions as $submission)
            @php
                $submission = (array)$submission;
                $submissionID = $submission[\App\Utilities\Constants::FLD_SUBMISSIONS_JUDGE_SUBMISSION_ID];
                $username = $submission[\App\Utilities\Constants::FLD_USERS_USERNAME];
                $problemName = $submission[\App\Utilities\Constants::FLD_SUBMISSIONS_PROBLEM_NAME];
                $problemLink = \App\Utilities\Utilities::generateProblemLink(new \App\Models\Problem($submission));
                $judgeID = $submission[\App\Utilities\Constants::FLD_PROBLEMS_JUDGE_ID];
                $judgeData = \App\Utilities\Constants::JUDGES[$judgeID];
                $judgeName = $judgeData[\App\Utilities\Constants::JUDGE_NAME_KEY];
                $judgeLink = $judgeData[\App\Utilities\Constants::JUDGE_LINK_KEY];
                $verdictId = $submission[\App\Utilities\Constants::FLD_SUBMISSIONS_VERDICT];
                $verdictName = \App\Utilities\Constants::VERDICT_NAMES[$verdictId];
                $executionTime = $submission[\App\Utilities\Constants::FLD_SUBMISSIONS_EXECUTION_TIME];
                $consumedMemory = round($submission[\App\Utilities\Constants::FLD_SUBMISSIONS_CONSUMED_MEMORY] / 1024);
                $language = $submission[\App\Utilities\Constants::FLD_SUBMISSIONS_LANGUAGE_NAME];
                $submittedTime = $submission[\App\Utilities\Constants::FLD_SUBMISSIONS_SUBMISSION_TIME];
                $submittedTime = date('Y-m-d H:i:s', $submissionTime);
                $style = ($verdictId == \App\Utilities\Constants::VERDICT_ACCEPTED ? 'success' : '');
            @endphp

            <tr>
                {{--Submission ID--}}
                <td>{{ $submissionID }}</td>

                {{--Username--}}
                <td><a href="{{ route(\App\Utilities\Constants::ROUTES_PROFILE, $username) }}">{{ $username }}</a></td>

                {{--Problem name--}}
                <td><a href="{{ $problemLink }}" target="_blank">{{ $problemName }}</a></td>

                {{--Judge--}}
                <td><a href="{{ $judgeLink }}" target="_blank">{{ $judgeName }}</a></td>

                {{--Verdict--}}
                <td class="{{ $style }}">{{ $verdictName }}</td>

                {{--Execution time--}}
                <td>{{ $executionTime . ' ms' }}</td>

                {{--Consumed Memory--}}
                <td>{{ $consumedMemory . ' KB' }}</td>

                {{--Language--}}
                <td>{{ $language }}</td>

                {{--Submitted at time--}}
                <td>{{ $submittedTime }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

{{--Pagination--}}
{{ $submissions->appends(Request::all())->render() }}
