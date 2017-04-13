{{--Display single contest questions and announcements info--}}
<div class="container questions-table-container">
    <table class="table table-bordered">
        <thead>
        <tr>
            <th width="10%" class="text-center">Problem ID</th>
            <th>Question</th>
            <th class="text-center" width="10%">Admin</th>
            @if($isContestRunning)
                <th class="text-center" width="10%">Actions</th>
            @endif
        </tr>
        </thead>
        <tbody>
        @foreach ( $data['questions'] as $question)
            <tr class="{{$question[Constants::FLD_QUESTIONS_STATUS] ==  Constants::QUESTION_STATUS[Constants::QUESTION_STATUS_ANNOUNCEMENT_KEY] ? 'announcement':''}}">
                <td>{{$question[Constants::FLD_QUESTIONS_PROBLEM_ID]}}</td>
                <td class="text-left">
                    <h4>
                        <strong>{{$question[Constants::FLD_QUESTIONS_TITLE]}}</strong>
                    </h4><br/>
                    <p>{{$question[Constants::FLD_QUESTIONS_CONTENT]}}</p>
                    <br/>
                    @if(strlen($question[Constants::FLD_QUESTIONS_ANSWER])>0)
                        <blockquote>{{$question[Constants::FLD_QUESTIONS_ANSWER]}}</blockquote>
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
</div>