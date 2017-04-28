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

                        {{--Hidden fields--}}
                        <input type="hidden" name="problem_id" id="problem-id"/>
                        <input type="hidden" name="sheet_id" id="sheet-id"/>

                        {{--Code Editor--}}
                        <div class="form-group code-editor-container">
                            <pre id="code-editor" contenteditable="true"></pre>
                            <textarea class="form-control" name="problem_solution" id="problem-solution"
                                      cols="0"
                                      rows="0"></textarea>
                        </div>
                    </div>

                    {{--Buttons--}}
                    <div class="modal-footer">
                        {{--Languages List--}}
                        @if($isOwner)
                            <div class="pull-left">@include('groups.sheet_views.languages_list')</div>
                        @endif

                        {{--Save--}}
                        <button type="submit" class="btn btn-success" id="answer-model-submit-button">
                            Save
                        </button>

                        {{--Close--}}
                        <button type="button" class="btn btn-info" data-dismiss="modal">Close
                        </button>
                    </div>
                </form>
            @else
                {{--Non group admins--}}
                <div class="modal-body">

                    {{--Answer--}}
                    <div class="form-group code-editor-container">
                        <pre id="code-editor-members"></pre>
                    </div>
                </div>

                {{--Buttons--}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" data-dismiss="modal">Close
                    </button>
                </div>
            @endif

        </div>
    </div>
</div>