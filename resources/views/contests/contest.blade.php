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
                                               data-toggle="tab">Paricipants</a>
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
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                        </div>
                        <div class="row">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection