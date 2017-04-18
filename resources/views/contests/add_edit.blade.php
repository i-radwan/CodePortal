@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ Request::is('contest/add') ? 'Add' : '' }} {{ Request::is('contest/edit') ? 'Edit' : '' }}
                        Contest
                    </div>

                    <div class="panel-body">
                        <form class="form-horizontal" id="add-edit-contest-form" role="form" method="POST"
                              action="{{ url('contest/add') }}">
                            {{ csrf_field() }}
                            <input type="hidden" id="problems-ids-hidden" name="problems_ids"/>
                            <input type="hidden" id="organisers-ids-hidden" name="organisers"/>
                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }} has-feedback">
                                <label for="name" class="col-md-4 control-label">Name</label>

                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control" name="name"
                                           value="{{ old('name') }}" placeholder="Name" required autofocus>

                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('time') ? ' has-error' : '' }} has-feedback">
                                <label for="time" class="col-md-4 control-label">Time</label>

                                <div class="col-md-6">
                                    <input id="time" type="datetime" class="form-control datetimepicker" name="time"
                                           value="{{ old('time') }}" placeholder="Time" required>

                                    @if ($errors->has('time'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('time') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('duration') ? ' has-error' : '' }} has-feedback">
                                <label for="duration" class="col-md-4 control-label">Duration</label>

                                <div class="col-md-6">
                                    <input id="duration" type="number" class="form-control" name="duration"
                                           value="{{ old('duration') }}" placeholder="Duration (mins)..." required>

                                    @if ($errors->has('duration'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('duration') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('visibility') ? ' has-error' : '' }} has-feedback">
                                <label for="duration" class="col-md-4 control-label">Visibility</label>
                                <div class="col-md-6 visibility-div">
                                    <ul>
                                        <li>
                                            <input type="radio" value="0" id="public" name="visibility" checked>
                                            <label for="public">Public</label>

                                            <div class="check"></div>
                                        </li>

                                        <li>
                                            <input type="radio" value="1" id="private" name="visibility">
                                            <label for="private">Private</label>

                                            <div class="check">
                                                <div class="inside"></div>
                                            </div>
                                        </li>
                                    </ul>
                                    @if ($errors->has('visibility'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('visibility') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('problems') ? ' has-error' : '' }} has-feedback">
                                <label for="problems" class="col-md-12 control-label" style="text-align: center">Problems</label>
                                <br>
                                <br>
                                <div class="col-md-4">
                                    @include("contests.contest_views.add_edit_filter")
                                </div>
                                <div class="col-md-8">
                                    @include("problems.table")
                                    @if ($errors->has('problems'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('problems') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('organizers') ? ' has-error' : '' }} has-feedback">
                                <label for="organizers" class="col-md-4 control-label">Organizers</label>
                                <div class="col-md-6">
                                    @include("contests.contest_views.organisers")
                                </div>
                                <div class="col-md-6">
                                    @if ($errors->has('organizers'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('organizers') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button onclick="moveSessionDataToHiddenFields()" type="submit"
                                            class="btn btn-primary">
                                        Add
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
