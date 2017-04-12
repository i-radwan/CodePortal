@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    {{--Contest leave/delete links--}}

                    @if($data[Constants::SINGLE_CONTEST_EXTRA_KEY][Constants::SINGLE_CONTEST_IS_USER_OWNER])
                        <a onclick="return confirm('Are you sure?')"
                           href="{{url('/contest/delete/'.$data[Constants::SINGLE_CONTEST_CONTEST_KEY][Constants::SINGLE_CONTEST_ID_KEY])}}">
                            <span class="pull-right text-dark btn btn-link margin-5px">Delete</span>
                        </a>
                    @endif

                    @if($data[Constants::SINGLE_CONTEST_EXTRA_KEY][Constants::SINGLE_CONTEST_IS_USER_PARTICIPATING])
                        <a onclick="return confirm('Are you sure?')"
                           href="{{url('/contest/leave/'.$data[Constants::SINGLE_CONTEST_CONTEST_KEY][Constants::SINGLE_CONTEST_ID_KEY])}}">
                            <span class="pull-right text-dark btn btn-link margin-5px">Leave</span>
                        </a>

                    @elseif(Auth::user() && !$data[Constants::SINGLE_CONTEST_EXTRA_KEY][Constants::SINGLE_CONTEST_IS_USER_OWNER])
                        <a href="{{url('/contest/join/'.$data[Constants::SINGLE_CONTEST_CONTEST_KEY][Constants::SINGLE_CONTEST_ID_KEY])}}">
                            <span class="pull-right text-dark btn btn-link margin-5px">Join</span>
                        </a>
                    @endif


                    {{--//Contest leave/delete links//--}}

                    <div class="panel-heading">
                        {{$data[Constants::SINGLE_CONTEST_CONTEST_KEY][Constants::SINGLE_CONTEST_NAME_KEY]}}
                    </div>

                    <div class="panel-body">
                        {{--Alerts Part--}}
                        @if (count($errors) > 0)

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-danger">
                                        @foreach ($errors->all() as $error)
                                            <p>• {{ $error }}</p>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row contest-info">
                            <div class="col-md-6">
                                <p class="contest-time pull-right">{{$data[Constants::SINGLE_CONTEST_CONTEST_KEY][Constants::SINGLE_CONTEST_TIME_KEY]}}</p>
                            </div>
                            <div class="col-md-6 contest-details">
                                <p>
                                    <strong>Owner:</strong> {{$data[Constants::SINGLE_CONTEST_CONTEST_KEY][Constants::SINGLE_CONTEST_OWNER_KEY]}}
                                </p>
                                @if(count($data[Constants::SINGLE_CONTEST_CONTEST_KEY][Constants::SINGLE_CONTEST_ORGANIZERS_KEY])>0)
                                    <p>
                                        <strong>Organizers:</strong>
                                        @foreach($data[Constants::SINGLE_CONTEST_CONTEST_KEY][Constants::SINGLE_CONTEST_ORGANIZERS_KEY] as $organizer)
                                            {{$organizer}}@if(!$loop->last),@endif
                                        @endforeach
                                    </p>
                                @endif
                                <p><strong>Duration:</strong>
                                    {{$data[Constants::SINGLE_CONTEST_CONTEST_KEY][Constants::SINGLE_CONTEST_DURATION_KEY]}}
                                    hrs
                                </p>
                            </div>
                        </div>
                        <div class="row contest-tabs">
                            <div class="col-md-12">
                                <!-- Nav tabs -->
                                <div class="card">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li role="presentation" class="active">
                                            <a href="#problems" aria-controls="problems" role="tab" data-toggle="tab">Problems</a>
                                        </li>
                                        <li role="presentation">
                                            <a href="#standings" aria-controls="standings" role="tab" data-toggle="tab">Standings</a>
                                        </li>
                                        <li role="presentation">
                                            <a href="#status" aria-controls="status" role="tab"
                                               data-toggle="tab">Status</a>
                                        </li>
                                        <li role="presentation">
                                            <a href="#participants" aria-controls="participants" role="tab"
                                               data-toggle="tab">Participants</a>
                                        </li>
                                        <li role="presentation">
                                            <a href="#questions" aria-controls="questions" role="tab"
                                               data-toggle="tab">Questions</a>
                                        </li>
                                    </ul>

                                    <!-- Tab panes -->
                                    <div class="tab-content">
                                        <div role="tabpanel" class="tab-pane active" id="problems">
                                            Problems
                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="standings">
                                            Standings
                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="status">
                                            Status
                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="participants">
                                            <div class="container questions-table-container">
                                                <table class="table table-bordered table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th class="text-center" width="50%">Username</th>
                                                        <th class="text-center">Country</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach ( $data['participants'] as $participant)
                                                        <tr>
                                                            <td>{{$participant[Constants::FLD_USERS_USERNAME]}}</td>
                                                            <td>{{$participant[Constants::FLD_USERS_COUNTRY]}}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="questions">
                                            <div class="container questions-table-container">
                                                <table class="table table-bordered">
                                                    <thead>
                                                    <tr>
                                                        <th width="10%" class="text-center">Problem ID</th>
                                                        <th>Question</th>
                                                        <th class="text-center" width="10%">Admin</th>
                                                        @if($data[Constants::SINGLE_CONTEST_EXTRA_KEY][Constants::SINGLE_CONTEST_RUNNING_STATUS])
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
                                                            @if($data[Constants::SINGLE_CONTEST_EXTRA_KEY][Constants::SINGLE_CONTEST_RUNNING_STATUS])
                                                                <td>
                                                                    @if($data[Constants::SINGLE_CONTEST_EXTRA_KEY][Constants::SINGLE_CONTEST_IS_USER_AN_ORGANIZER])
                                                                        <button class="btn btn-primary question-answer-button"
                                                                                data-toggle="modal"
                                                                                data-target="#question-answer-model"
                                                                                onclick="$('#question-id').val('{{$question[Constants::FLD_QUESTIONS_ID]}}');$('#question-answer').val('{{$question[Constants::FLD_QUESTIONS_ANSWER]}}');">
                                                                            Answer
                                                                        </button>
                                                                    @endif
                                                                    @if($question[Constants::FLD_QUESTIONS_STATUS]==0
                                                                    && $data[Constants::SINGLE_CONTEST_EXTRA_KEY][Constants::SINGLE_CONTEST_IS_USER_AN_ORGANIZER]
                                                                    && strlen($question[Constants::FLD_QUESTIONS_ANSWER])>0)
                                                                        <a href="{{url('contest/question/announce/'.$question[Constants::FLD_QUESTIONS_ID])}}">
                                                                            <button class="btn btn-primary">Announce
                                                                            </button>
                                                                        </a>
                                                                    @elseif($question[Constants::FLD_QUESTIONS_STATUS]==1
                                                                    && $data[Constants::SINGLE_CONTEST_EXTRA_KEY][Constants::SINGLE_CONTEST_IS_USER_AN_ORGANIZER])
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
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{--Show questions section when contest is running only and when user is participant--}}
                        @if($data[Constants::SINGLE_CONTEST_EXTRA_KEY][Constants::SINGLE_CONTEST_RUNNING_STATUS]
                        && $data[Constants::SINGLE_CONTEST_EXTRA_KEY][Constants::SINGLE_CONTEST_IS_USER_PARTICIPATING])

                            <div class="centered">
                                <div class="row">
                                    <div class="contest-ask-question col-md-12 form-group">
                                        <div><span class="contest-ask-question-title">Ask Question!</span></div>
                                        <form class="form-horizontal"
                                              action="{{url('contest/question/'.$data[Constants::SINGLE_CONTEST_CONTEST_KEY][Constants::SINGLE_CONTEST_ID_KEY])}}"
                                              method="post">
                                            {{csrf_field()}}
                                            <input type="text" name="title" placeholder="Title..." class="form-control"
                                                   required/>
                                            <textarea name="content" cols="30" rows="5" class="form-control"
                                                      placeholder="Question content..." required></textarea>

                                            {{--Display Errors--}}
                                            @if(Session::has('question-error'))
                                                <p class="error-msg">{{ Session::get('question-error') }}</p>
                                            @endif
                                            <input type="submit" value="Ask Question!" class="btn btn-primary"/>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @if ($data[Constants::SINGLE_CONTEST_EXTRA_KEY][Constants::SINGLE_CONTEST_IS_USER_AN_ORGANIZER])
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
        @endif

    </div>
@endsection