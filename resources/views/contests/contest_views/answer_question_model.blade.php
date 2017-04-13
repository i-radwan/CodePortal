{{--Answer Question Modal--}}
<div id="question-answer-model" class="modal fade question-answer-mode" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <form method="post" action="{{url('contest/question/answer')}}" name="answerForm">
            {{csrf_field()}}
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Answer Question</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="question_id" id="question-id"/>
                    <div class="form-group">
                        <div class="cols-sm-12">
                            <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info" aria-hidden="true"></i>
                                            </span>
                                <textarea class="form-control" name="question_answer" id="question-answer"
                                          cols="5"
                                          rows="10" placeholder="Answer..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" id="answer-model-submit-button">
                        Answer
                    </button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>