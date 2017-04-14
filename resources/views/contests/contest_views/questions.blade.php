{{--Display single contest questions and announcements info--}}
<table class="table table-bordered questions-table text-center table-condensed">
    <thead>
    <tr>
        <th class="text-center">Problem</th>
        <th class="questions-table-question-th">Question</th>
        <th class="text-center">Admin</th>
        @if($isContestRunning)
            <th class="text-center">Actions</th>
        @endif
    </tr>
    </thead>
    <tbody>
    @foreach ( $data['questions'] as $question)
        <tr class="{{$question[Constants::FLD_QUESTIONS_STATUS] ==  Constants::QUESTION_STATUS[Constants::QUESTION_STATUS_ANNOUNCEMENT_KEY] ? 'announcement':''}}">
            <td>{{$question[Constants::FLD_QUESTIONS_PROBLEM_ID]}}</td>
            <td class="text-left">
                <h4 class="break-word">
                    <strong>{{$question[Constants::FLD_QUESTIONS_TITLE]}}</strong>
                </h4><br/>
                <p class="break-word">{{$question[Constants::FLD_QUESTIONS_CONTENT]}}</p>
                <br/>
                @if(strlen($question[Constants::FLD_QUESTIONS_ANSWER])>0)
                    <blockquote class="break-word">{{$question[Constants::FLD_QUESTIONS_ANSWER]}}</blockquote>
                @endif
            </td>
            <td>{{$question[Constants::FLD_QUESTIONS_ADMIN_ID]}}</td>
            @if($isContestRunning)
                <td>
                    @if($isOrganizer)
                        <button class="btn btn-primary question-answer-button"
                                data-toggle="modal"
                                data-target="#question-answer-model"
                                onclick="$('#question-id').val('{{$question[Constants::FLD_QUESTIONS_ID]}}');$('#question-answer').val('{{$question[Constants::FLD_QUESTIONS_ANSWER]}}');">
                            Answer
                        </button>
                    @endif
                    @if($question[Constants::FLD_QUESTIONS_STATUS]==0
                    && $isOrganizer
                    && strlen($question[Constants::FLD_QUESTIONS_ANSWER])>0)
                        <a href="{{url('contest/question/announce/'.$question[Constants::FLD_QUESTIONS_ID])}}">
                            <button class="btn btn-primary">Announce
                            </button>
                        </a>
                    @elseif($question[Constants::FLD_QUESTIONS_STATUS]==1
                    && $isOrganizer)
                        <a href="{{url('contest/question/renounce/'.$question[Constants::FLD_QUESTIONS_ID])}}">
                            <button class="btn btn-primary">Renounce
                            </button>
                        </a>
                    @endif
                </td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>
