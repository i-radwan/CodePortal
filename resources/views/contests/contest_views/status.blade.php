{{--Display single contest problems info--}}
<table class="table table-bordered table-hover text-center">
    <thead>
        <tr>
            <th class="text-center">ID</th>
            <th class="text-center">Contestant</th>
            <th class="text-center">Problem</th>
            <th class="text-center">Judge</th>
            <th class="text-center">Verdict</th>
            <th class="text-center">Execution Time</th>
            <th class="text-center">Consumed Memory</th>
            <th class="text-center">Language</th>
            <th class="text-center">Time</th>
        </tr>
    </thead>
    <tbody>
        @foreach($status as $submission)
            @php
                $submission = (array)$submission;
                $submissionID = $submission[\App\Utilities\Constants::FLD_SUBMISSIONS_JUDGE_SUBMISSION_ID];
                $submissionUsername = $submission[\App\Utilities\Constants::FLD_USERS_USERNAME];
                $submissionProblemName = $submission[\App\Utilities\Constants::FLD_SUBMISSIONS_PROBLEM_NAME];
                $submissionJudgeID = $submission[\App\Utilities\Constants::FLD_PROBLEMS_JUDGE_ID];
                $submissionJudgeName = \App\Utilities\Constants::JUDGES[$submissionJudgeID][\App\Utilities\Constants::JUDGE_NAME_KEY];
                $submissionVerdictId = $submission[\App\Utilities\Constants::FLD_SUBMISSIONS_VERDICT];
                $submissionVerdictName = \App\Utilities\Constants::VERDICT_NAMES[$submissionVerdictId];
                $submissionExecutionTime = $submission[\App\Utilities\Constants::FLD_SUBMISSIONS_EXECUTION_TIME];
                $submissionConsumedMemory = $submission[\App\Utilities\Constants::FLD_SUBMISSIONS_CONSUMED_MEMORY] / 1024;
                $submissionLanguage = $submission[\App\Utilities\Constants::FLD_SUBMISSIONS_LANGUAGE_NAME];
                $submissionTime = $submission[\App\Utilities\Constants::FLD_SUBMISSIONS_SUBMISSION_TIME];
                $submissionTime = date('Y-m-d H:i:s', $submissionTime);
            @endphp

            <tr>
                <td>{{ $submissionID }}</td>
                <td>{{ $submissionUsername }}</td>
                <td>{{ $submissionProblemName }}</td>
                <td>{{ $submissionJudgeName }}</td>
                <td class="{{ $submissionVerdictId == Constants::VERDICT_ACCEPTED ? 'success' : '' }}">
                    {{ $submissionVerdictName }}
                </td>
                <td>{{ $submissionExecutionTime . ' ms' }}</td>
                <td>{{ $submissionConsumedMemory . ' KB' }}</td>
                <td>{{ $submissionLanguage }}</td>
                <td>{{ $submissionTime }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
