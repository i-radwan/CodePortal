{{--Answer Problem Modal--}}
<div id="problem-solution-model" class="modal problem-solution-mode">
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
                        <div class="form-group code-editor-container">
                            <pre id="code-editor">
                            </pre>
                            <textarea class="form-control" name="problem_solution" id="problem-solution"
                                      cols="0"
                                      rows="0"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        @if($isOwner)
                            <div class="pull-left">@include('groups.sheet_views.languages_list')</div>
                        @endif
                        <button type="submit" class="btn btn-success" id="answer-model-submit-button">
                            Save
                        </button>
                        <button type="button" class="btn btn-info" data-dismiss="modal">Close
                        </button>
                    </div>
                </form>
            @else
                <div class="modal-body">
                    <div class="form-group code-editor-container">
                            <pre id="code-editor-members">
                    </pre>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close
                    </button>
                </div>
            @endif

        </div>
    </div>
</div>
<script>

</script>