@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">{{ Request::is('contests/create') ? 'Add' : '' }} {{ Request::is('contest/edit') ? 'Edit' : '' }}
                {{ isset($contest)? $contest[\App\Utilities\Constants::FLD_CONTESTS_NAME]:'Contest' }}
            </div>

            <div class="panel-body">
                {{--Alerts Part--}}
                @include('components.alert')

                <form class="form-horizontal add-edit-contest-form" id="add-edit-contest-form"
                      role="form"
                      method="POST"
                      action="{{ $formURL }}">

                    {{ csrf_field() }}

                    <input type="hidden" id="problems-ids-hidden" name="problems_ids"/>
                    <input type="hidden" id="organisers-ids-hidden" name="organisers"/>
                    <input type="hidden" id="invitees-ids-hidden" name="invitees"/>

                    {{--Name--}}
                    <div class="form-group">
                        <label for="name" class="col-md-2 control-label">Name</label>

                        <div class="col-md-4">
                            <input id="name" class="form-control"
                                   type="text"
                                   name="name"
                                   value="{{ old('name') }}"
                                   placeholder="Name" required autofocus>
                        </div>
                    </div>

                    {{--Time--}}
                    <div class="form-group">
                        <label for="time" class="col-md-2 control-label">Time</label>

                        <div class="col-md-4">
                            <input id="time" class="form-control datetimepicker"
                                   type="datetime" name="time"
                                   value="{{ old('time') }}"
                                   placeholder="Time" required>
                        </div>
                    </div>

                    {{--Duration--}}
                    <div class="form-group">
                        <label for="duration" class="col-md-2 control-label">Duration</label>

                        <div class="col-md-2">
                            <input id="duration" class="timing" type="text" name="duration"/>
                        </div>
                    </div>

                    {{--Add organizers and control visibility if not group contest--}}
                    @if(!isset($group))
                        {{--Visibility--}}
                        <div class="form-group">
                            <label for="duration" class="col-md-2 control-label">Visibility</label>

                            <div class="col-md-4 visibility-div">
                                <ul>
                                    <li>
                                        <input id="public_visibility" type="radio"
                                               name="visibility" value="0" checked>
                                        <label for="public_visibility">Public</label>
                                        <div class="check"></div>
                                    </li>

                                    <li>
                                        <input id="private_visibility" type="radio"
                                               name="visibility" value="1">
                                        <label for="private_visibility">Private</label>
                                        <div class="check"></div>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        {{--Organisers--}}
                        <div class="form-group">
                            <label for="organizers" class="col-md-2 control-label text-left">Organizers</label>

                            <div class="col-md-4">
                                {{--Organinsers Auto Complete--}}
                                @include('components.auto_complete', [
                                    'itemsType' => 'organisers',
                                    'itemName' => 'Organiser',
                                    'itemsLink' => route(\App\Utilities\Constants::ROUTES_CONTESTS_ORGANIZERS_AUTO_COMPLETE),
                                    'hiddenID' => '',
                                    'hiddenName' => ''
                                ])
                            </div>
                        </div>

                        {{--Invitees (to be invited to private contest)--}}
                        <div class="form-group invitees-input-div" id="invitees-input-div">
                            <label for="invitees" class="col-md-2 control-label text-left">Invitees</label>

                            <div class="col-md-4">
                                {{--Organinsers Auto Complete--}}
                                @include('components.auto_complete', [
                                    'itemsType' => 'invitees',
                                    'itemName' => 'Invitee',
                                    'itemsLink' => route(\App\Utilities\Constants::ROUTES_CONTESTS_INVITEES_AUTO_COMPLETE),
                                    'hiddenID' => '',
                                    'hiddenName' => ''
                                ])
                            </div>
                        </div>
                    @endif

                    {{--Problems--}}
                    @include('components.problems_selector')

                    {{--Submit--}}
                    <div class="form-group">
                        <div class="col-md-12 text-center">
                            <button class="btn btn-primary" id="add-save-contest"
                                    type="submit"
                                    onclick="app.moveContestSessionDataToHiddenFields()">
                                @if(!isset($contest))
                                    Add
                                @else
                                    Save
                                @endif
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <span class="page-distinguishing-element" id="add-edit-contest-page-hidden-element"
          data-selected-tags="{{ ($selected_tags)?$selected_tags : '' }}"
          data-selected-judges="{{ ($selected_judges)?$selected_judges : '' }}"
          @if(isset($contest))
          data-name="{{ $contest[\App\Utilities\Constants::FLD_CONTESTS_NAME] }}"
          data-time="{{ $contest[\App\Utilities\Constants::FLD_CONTESTS_TIME] }}"
          data-duration="{{ $contest[\App\Utilities\Constants::FLD_CONTESTS_DURATION] }}"
          data-visibility="{{ $contest[\App\Utilities\Constants::FLD_CONTESTS_VISIBILITY] }}"
          data-organizers="{{ $contest->organizers()->pluck(\App\Utilities\Constants::FLD_USERS_USERNAME) }}"
          data-problems="{{$contest->problems()->pluck(\App\Utilities\Constants::FLD_PROBLEMS_ID) }}"
          @endif>
    </span>
@endsection
