{{--Display single contest questions and announcements info--}}
<table class="table table-bordered questions-table text-center">
    <thead>
    <tr>
        <th class="text-center">Problem</th>
        <th class="questions-table-question-cell">Question</th>
        <th class="text-center">Admin</th>
        @if($isOwnerOrOrganizer)
            <th class="text-center">Actions</th>
        @endif
    </tr>
    </thead>

    <tbody>
    @foreach($questions as $question)
        @php
            $questionID = $question[\App\Utilities\Constants::FLD_QUESTIONS_ID];
            $questionTitle = $question[\App\Utilities\Constants::FLD_QUESTIONS_TITLE];
            $questionContent = $question[\App\Utilities\Constants::FLD_QUESTIONS_CONTENT];
            $questionAnswer = $question[\App\Utilities\Constants::FLD_QUESTIONS_ANSWER];
            $questionStatus = $question[\App\Utilities\Constants::FLD_QUESTIONS_STATUS];

            $problemID = $question[\App\Utilities\Constants::FLD_QUESTIONS_PROBLEM_ID];
            $problem = \App\Models\Problem::find($problemID);
            $problemName = $problem[\App\Utilities\Constants::FLD_PROBLEMS_NAME];

            $adminID = $question[\App\Utilities\Constants::FLD_QUESTIONS_ADMIN_ID];
            $admin = ($adminID) ? \App\Models\User::find($adminID) : null;
            $adminName = ($admin) ? $admin[\App\Utilities\Constants::FLD_USERS_USERNAME] : '';

            $style = ($questionStatus == \App\Utilities\Constants::QUESTION_STATUS_ANNOUNCEMENT) ? 'announcement' : '';
        @endphp


        <tr class="{{ $style }}">

            {{--Problem Name--}}
            <td>{{ $problemName }}</td>

            {{--Question and Answer--}}
            <td class="text-left questions-table-question-cell">
                {{--Question Title--}}
                <h4 class="break-word">
                    <strong>{{ $questionTitle }}</strong>
                </h4>

                <br/>

                {{--Question Contest--}}
                <p class="break-word">
                    {{ $questionContent }}
                </p>

                <br/>

                {{--Question Answer--}}
                @if(strlen($questionAnswer) > 0)
                    <blockquote class="break-word">{{ $questionAnswer }}</blockquote>
                @endif
            </td>

            {{--Admin Name--}}
            <td>{{ $adminName }}</td>

            {{--Organizer Actions--}}
            @if($isOwnerOrOrganizer)
                <td>
                    <button class="btn btn-primary testing-question-action-button answer"
                            data-toggle="modal"
                            data-target="#question-answer-model"
                            onclick="$('#question-id').val('{{ $questionID }}');$('#question-answer').val('{{ ($questionAnswer != "") ? addslashes($questionAnswer) : "Read the problem statement!"}}');">
                        Answer
                    </button>

                    {{--Announce Form--}}
                    @if($questionStatus == \App\Utilities\Constants::QUESTION_STATUS_NORMAL && strlen($questionAnswer) > 0)
                        @include('components.action_form', [
                            'url' => route(\App\Utilities\Constants::ROUTES_CONTESTS_QUESTIONS_ANNOUNCE, $questionID),
                            'method' => 'PUT',
                            'confirm' => false,
                            'btnClasses' => 'btn btn-primary testing-question-action-button announce',
                            'btnIDs' => '',
                            'btnTxt' => 'Announce'
                        ])
                    {{--Renounce Form--}}
                    @elseif($questionStatus == \App\Utilities\Constants::QUESTION_STATUS_ANNOUNCEMENT)
                        @include('components.action_form', [
                            'url' => route(\App\Utilities\Constants::ROUTES_CONTESTS_QUESTIONS_RENOUNCE, $questionID),
                            'method' => 'PUT',
                            'confirm' => false,
                            'btnClasses' => 'btn btn-primary testing-question-action-button renounce',
                            'btnIDs' => '',
                            'btnTxt' => 'Renounce'
                        ])
                    @endif
                </td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>
