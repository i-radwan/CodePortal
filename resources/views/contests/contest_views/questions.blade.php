{{--Display single contest questions and announcements info--}}
<table class="table table-bordered questions-table text-center table-condensed">
    <thead>
    <tr>
        <th class="text-center">Problem</th>
        <th class="questions-table-question-cell">Question</th>
        <th class="text-center">Admin</th>
        @if($isContestRunning && $isOwnerOrOrganizer)
            <th class="text-center">Actions</th>
        @endif
    </tr>
    </thead>
    <tbody>
    @foreach($questions as $question)

        {{--Define some vars--}}
        @php
            $answer = $question[Constants::FLD_QUESTIONS_ANSWER];
            $questionID = $question[Constants::FLD_QUESTIONS_ID];
        @endphp


        <tr class="{{$question[Constants::FLD_QUESTIONS_STATUS] ==  Constants::QUESTION_STATUS_ANNOUNCEMENT ? 'announcement':''}}">

            {{--Problem ID--}}
            <td>{{$question[Constants::FLD_QUESTIONS_PROBLEM_ID]}}</td>

            {{--Question+Answer--}}
            <td class="text-left questions-table-question-cell">

                {{--Question Title--}}
                <h4 class="break-word">
                    <strong>{{$question[Constants::FLD_QUESTIONS_TITLE]}}</strong>
                </h4><br/>

                {{--Question Contest--}}
                <p class="break-word">{{$question[Constants::FLD_QUESTIONS_CONTENT]}}</p>

                <br/>

                {{--Question Answer--}}
                @if(strlen($answer)>0)
                    <blockquote class="break-word">{{$answer}}</blockquote>
                @endif
            </td>

            {{--Organizer ID--}}
            <td>{{$question[Constants::FLD_QUESTIONS_ADMIN_ID]}}</td>

            {{--Organizer Actions--}}
            @if($isContestRunning && $isOwnerOrOrganizer)
                <td>

                    <button class="btn btn-primary testing-question-action-button answer"
                            data-toggle="modal"
                            data-target="#question-answer-model"
                            onclick="$('#question-id').val('{{$questionID}}');$('#question-answer').val('{{($answer != "")? addslashes($answer): "Re-read the problem statement!"}}');">
                        Answer
                    </button>

                    {{--Announce Form--}}
                    @if($question[Constants::FLD_QUESTIONS_STATUS]==0 && strlen($answer)>0)
                        @include('components.action_form', ['url' => route(\App\Utilities\Constants::ROUTES_CONTESTS_QUESTIONS_ANNOUNCE, $questionID), 'method' => 'PUT', 'confirm' => false, 'btnClasses' => 'btn btn-primary testing-question-action-button announce', 'btnIDs' => '', 'btnTxt' => 'Announce'])

                        {{--Renounce Form--}}
                    @elseif($question[Constants::FLD_QUESTIONS_STATUS]==1)
                        @include('components.action_form', ['url' => route(\App\Utilities\Constants::ROUTES_CONTESTS_QUESTIONS_RENOUNCE, $questionID), 'method' => 'PUT', 'confirm' => false, 'btnClasses' => 'btn btn-primary testing-question-action-button renounce', 'btnIDs' => '', 'btnTxt' => 'Renounce'])

                    @endif
                </td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>
