{{--Answer Question Modal--}}
<div id="problem-solution-model" class="modal fade problem-solution-mode" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Problem Solution</h4>
            </div>

            @if($isOwner)
                <form method="post" action="{{url('sheet/problem/solution')}}" name="answerForm">
                    {{csrf_field()}}
                    <div class="modal-body">
                        <input type="hidden" name="problem_id" id="problem-id"/>
                        <input type="hidden" name="sheet_id" id="sheet-id"/>
                        {{--ToDo : Add languages list--}}
                        <div class="form-group">
                            <div class="cols-sm-12">
                                <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-info" aria-hidden="true"></i>
                                </span>
                                    {{--ToDo : use code editor later--}}
                                    <textarea class="form-control" name="problem_solution" id="problem-solution"
                                              cols="5"
                                              rows="10" placeholder="Solution..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="answer-model-submit-button">
                            Save
                        </button>
                        <button type="button" class="btn btn-info" data-dismiss="modal">Close
                        </button>
                    </div>
                </form>
            @else
                <div class="modal-body">
                    <p id="problem-solution-p"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close
                    </button>
                </div>
            @endif

        </div>
    </div>
</div>