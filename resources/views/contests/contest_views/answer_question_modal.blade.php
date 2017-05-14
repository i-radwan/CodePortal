{{--Answer Question Modal--}}
<div id="question-answer-model" class="modal fade question-answer-mode" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <form method="POST"
              action="{{ route(\App\Utilities\Constants::ROUTES_CONTESTS_QUESTIONS_ANSWER_STORE) }}"
              name="answerForm">
            {{csrf_field()}}

            <div class="modal-content">
                {{--Header--}}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Answer Question</h4>
                </div>

                {{--Body--}}
                <div class="modal-body">
                    <input type="hidden" name="question_id" id="question-id"/>

                    <div class="form-group">
                        <div class="cols-sm-12">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-info" aria-hidden="true"></i>
                                </span>

                                <textarea class="form-control"
                                          name="question_answer"
                                          id="question-answer"
                                          cols="5" rows="10"
                                          placeholder="Answer...">
                                </textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{--Footer--}}
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="answer-model-submit-button">
                        Answer
                    </button>

                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>