{{--Display single contest questions and announcements info--}}
<table class="table table-bordered questions-table text-center table-condensed">
    <thead>
    <tr>
        <th class="text-center">Problem</th>
        <th class="questions-table-question-cell">Question</th>
        <th class="text-center">Admin</th>
        @if($isContestRunning)
            <th class="text-center">Actions</th>
        @endif
    </tr>
    </thead>
    <tbody>
    @foreach($questions as $question)
        {{--define some vars--}}
        @php
            $answer = $question[Constants::FLD_QUESTIONS_ANSWER];
            $questionID = $question[Constants::FLD_QUESTIONS_ID];
        @endphp
        <tr class="{{$question[Constants::FLD_QUESTIONS_STATUS] ==  Constants::QUESTION_STATUS[Constants::QUESTION_STATUS_ANNOUNCEMENT_KEY] ? 'announcement':''}}">
            <td>{{$question[Constants::FLD_QUESTIONS_PROBLEM_ID]}}</td>

            <td class="text-left questions-table-question-cell">
                <h4 class="break-word">
                    <strong>{{$question[Constants::FLD_QUESTIONS_TITLE]}}</strong>
                </h4><br/>

                <p class="break-word">{{$question[Constants::FLD_QUESTIONS_CONTENT]}}</p>

                <br/>

                @if(strlen($answer)>0)
                    <blockquote class="break-word">{{$answer}}</blockquote>
                @endif
            </td>

            <td>{{$question[Constants::FLD_QUESTIONS_ADMIN_ID]}}</td>

            @if($isContestRunning)
                <td>

                    @if($isOwnerOrOrganizer)
                        <button class="btn btn-primary question-answer-button"
                                data-toggle="modal"
                                data-target="#question-answer-model"
                                onclick="$('#question-id').val('{{$questionID}}');$('#question-answer').val('{{($answer != "")? $answer: "Re-read the problem statement!"}}');">
                            Answer
                        </button>
                    @endif

                    @if($question[Constants::FLD_QUESTIONS_STATUS]==0 && $isOwnerOrOrganizer && strlen($answer)>0)
                        <form action="{{url('contest/question/announce/'.$questionID)}}"
                              method="post">{{method_field('PUT')}}
                            {{csrf_field()}}
                            <button type="submit" class="btn btn-primary">Announce
                            </button>
                        </form>

                    @elseif($question[Constants::FLD_QUESTIONS_STATUS]==1 && $isOwnerOrOrganizer)
                        <form action="{{url('contest/question/renounce/'.$questionID)}}"
                              method="post">{{method_field('PUT')}}
                            {{csrf_field()}}
                            <button type="submit" class="btn btn-primary">Renounce
                            </button>
                        </form>
                    @endif
                </td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>
