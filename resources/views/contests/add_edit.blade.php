@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ Request::is('contest/add') ? 'Add' : '' }} {{ Request::is('contest/edit') ? 'Edit' : '' }}
                        {{ isset($contest)? $contest[\App\Utilities\Constants::FLD_CONTESTS_NAME]:'Contest' }}
                    </div>

                    <div class="panel-body">
                        {{--Alerts Part--}}
                        @include('components.alert')

                        <form class="form-horizontal add-edit-contest-form" id="add-edit-contest-form" role="form"
                              method="POST"
                              action="{{ $formURL }}">
                            {{ csrf_field() }}
                            <input type="hidden" id="problems-ids-hidden" name="problems_ids"/>
                            <input type="hidden" id="organisers-ids-hidden" name="organisers"/>
                            <input type="hidden" id="invitees-ids-hidden" name="invitees"/>

                            {{--Name--}}
                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }} has-feedback">
                                <label for="name" class="col-md-2 control-label">Name</label>

                                <div class="col-md-4">
                                    <input id="name" type="text" class="form-control" name="name"
                                           value="{{ old('name') }}" placeholder="Name" required autofocus>

                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{--Time--}}
                            <div class="form-group{{ $errors->has('time') ? ' has-error' : '' }} has-feedback">
                                <label for="time" class="col-md-2 control-label">Time</label>

                                <div class="col-md-4">
                                    <input id="time" type="datetime" class="form-control datetimepicker" name="time"
                                           value="{{ old('time') }}" placeholder="Time" required>

                                    @if ($errors->has('time'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('time') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            {{--Duration--}}
                            <div class="form-group{{ $errors->has('duration') ? ' has-error' : '' }} has-feedback">
                                <label for="duration" class="col-md-2 control-label">Duration</label>

                                <div class="col-md-2">
                                    <input id="duration" type="number" class="duration-picker"
                                           name="duration" placeholder="Duration (mins)..." required>

                                    @if ($errors->has('duration'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('duration') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            {{--Add organizers and control visibility if not group contest--}}
                            @if(!isset($group))
                                {{--Visibility--}}
                                <div class="form-group{{ $errors->has('visibility') ? ' has-error' : '' }} has-feedback">
                                    <label for="duration" class="col-md-2 control-label">Visibility</label>
                                    <div class="col-md-4 visibility-div">
                                        <ul>
                                            <li>
                                                <input type="radio" value="0" id="public_visibility" name="visibility"
                                                       checked>
                                                <label for="public_visibility">Public</label>

                                                <div class="check"></div>
                                            </li>

                                            <li>
                                                <input type="radio" value="1" id="private_visibility" name="visibility">
                                                <label for="private_visibility">Private</label>

                                                <div class="check"></div>
                                            </li>
                                        </ul>

                                        @if ($errors->has('visibility'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('visibility') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                {{--Organisers--}}
                                <div class="form-group{{ $errors->has('organizers') ? ' has-error' : '' }} has-feedback">
                                    <label for="organizers" class="col-md-2 control-label text-left">Organizers</label>
                                    <div class="col-md-4">

                                        {{--Organinsers Auto Complete--}}
                                        @include('components.auto_complete', ['itemsType' => 'organisers', 'itemName' => 'Organiser', 'itemsLink' => url('contest/add/organisers_auto_complete'), 'hiddenID' => '', 'hiddenName' => ''])


                                    @if ($errors->has('organizers'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('organizers') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{--Invitees (to be invited to private contest)--}}
                                <div id="invitees-input-div"
                                     class="invitees-input-div form-group{{ $errors->has('invitees') ? ' has-error' : '' }} has-feedback">
                                    <label for="invitees" class="col-md-2 control-label text-left">Invitees</label>
                                    <div class="col-md-4">

                                        {{--Organinsers Auto Complete--}}
                                        @include('components.auto_complete', ['itemsType' => 'invitees', 'itemName' => 'Invitee', 'itemsLink' => url('contest/add/invitees_auto_complete'), 'hiddenID' => '', 'hiddenName' => ''])

                                        @if ($errors->has('invitees'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('invitees') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>

                            @endif
                            {{--Problems--}}
                            @include('components.problems_selector')

                            {{--Submit--}}
                            <div class="form-group">
                                <div class="col-md-12 text-center">
                                    <button onclick="app.moveSessionDataToHiddenFields()" type="submit"
                                            class="btn btn-primary" id="add-save-contest">
                                        @if(!isset($contest))
                                            Add
                                        @else Save
                                        @endif
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <span class="page-distinguishing-element" id="add-edit-contest-page-hidden-element"
          data-selected-tags="{{($selected_tags)?$selected_tags:''}}"
          data-selected-judges="{{($selected_judges)?$selected_judges:''}}"
          @if(isset($contest))
          data-name="{{$contest[\App\Utilities\Constants::FLD_CONTESTS_NAME]}}"
          data-time="{{$contest[\App\Utilities\Constants::FLD_CONTESTS_TIME]}}"
          data-duration="{{$contest[\App\Utilities\Constants::FLD_CONTESTS_DURATION] * 60}}"
          data-visibility="{{$contest[\App\Utilities\Constants::FLD_CONTESTS_VISIBILITY]}}"
          data-organizers="{{$contest->organizers()->pluck(\App\Utilities\Constants::FLD_USERS_USERNAME)}}"
          data-problems="{{$contest->problems()->pluck(\App\Utilities\Constants::FLD_PROBLEMS_ID)}}"
            @endif
    ></span>
@endsection
