@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    {{--Contest leave/delete links--}}

                    @if($data[Constants::SINGLE_CONTEST_EXTRA_KEY][Constants::SINGLE_CONTEST_DELETE_BTN_VISIBLE_KEY])
                        <a onclick="return confirm('Are you sure?')"
                           href="{{url('/contest/delete/'.$data[Constants::SINGLE_CONTEST_CONTEST_KEY][Constants::SINGLE_CONTEST_ID_KEY])}}"><span
                                    class="pull-right text-dark btn btn-link margin-5px">Delete</span></a>
                    @endif

                    @if($data[Constants::SINGLE_CONTEST_EXTRA_KEY][Constants::SINGLE_CONTEST_LEAVE_BTN_VISIBLE_KEY])
                        <a onclick="return confirm('Are you sure?')"
                           href="{{url('/contest/leave/'.$data[Constants::SINGLE_CONTEST_CONTEST_KEY][Constants::SINGLE_CONTEST_ID_KEY])}}"><span
                                    class="pull-right text-dark btn btn-link margin-5px">Leave</span></a>
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
                                <p>
                                    <strong>Oragnizers:</strong>
                                    @foreach($data[Constants::SINGLE_CONTEST_CONTEST_KEY][Constants::SINGLE_CONTEST_ORGANIZERS_KEY] as $organizer)
                                        {{$organizer}}@if(!$loop->last),@endif
                                    @endforeach
                                </p>
                                <p><strong>Duration:</strong>
                                    {{$data[Constants::SINGLE_CONTEST_CONTEST_KEY][Constants::SINGLE_CONTEST_DURATION_KEY]}}
                                </p>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection