@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    {{--Contest leave/delete links--}}

                    @if($data[Constants::SINGLE_CONTEST_EXTRA_KEY][Constants::SINGLE_CONTEST_DELETE_BTN_VISIBLE_KEY])
                        <a onclick="return confirm('Are you sure?')"
                           href="{{url('/contest/delete/'.$data[Constants::SINGLE_CONTEST_CONTEST_KEY][Constants::SINGLE_CONTEST_ID_KEY])}}">
                            <span class="pull-right text-dark btn btn-link margin-5px">Delete</span>
                        </a>
                    @endif

                    @if($data[Constants::SINGLE_CONTEST_EXTRA_KEY][Constants::SINGLE_CONTEST_LEAVE_BTN_VISIBLE_KEY])
                        <a onclick="return confirm('Are you sure?')"
                           href="{{url('/contest/leave/'.$data[Constants::SINGLE_CONTEST_CONTEST_KEY][Constants::SINGLE_CONTEST_ID_KEY])}}">
                            <span class="pull-right text-dark btn btn-link margin-5px">Leave</span>
                        </a>

                    @elseif(Auth::user() && !$data[Constants::SINGLE_CONTEST_EXTRA_KEY][Constants::SINGLE_CONTEST_DELETE_BTN_VISIBLE_KEY])
                        <a href="{{url('/contest/join/'.$data[Constants::SINGLE_CONTEST_CONTEST_KEY][Constants::SINGLE_CONTEST_ID_KEY])}}">
                            <span class="pull-right text-dark btn btn-link margin-5px">Join</span>
                        </a>
                    @endif


                    {{--//Contest leave/delete links//--}}

                    <div class="panel-heading">
                        {{$data[Constants::SINGLE_CONTEST_CONTEST_KEY][Constants::SINGLE_CONTEST_NAME_KEY]}}
                    </div>

                    <div class="panel-body">
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
                                        <div role="tabpanel" class="tab-pane active" id="problems">Lorem Ipsum is simply
                                            dummy text of the printing and typesetting industry.
                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="standings">Lorem Ipsum is simply dummy
                                            text of the printing and typesetting industry.
                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="status">Lorem Ipsum is simply dummy
                                            text of the printing and typesetting industry.
                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="participants">

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
                                                                        <button class="btn btn-primary">Answer</button>
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
                        {{--Show questions section when contest is running only--}}
                        @if($data[Constants::SINGLE_CONTEST_EXTRA_KEY][Constants::SINGLE_CONTEST_RUNNING_STATUS])
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
                                            @if (count($errors) > 0)
                                                @foreach ($errors->all() as $error)
                                                    <p class="error-msg">{{ $error }}</p>
                                                @endforeach
                                            @endif

                                            <input type="submit" value="Ask Question!" class="btn btn-primary"/>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="row">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection