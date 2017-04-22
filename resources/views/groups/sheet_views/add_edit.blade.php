@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">{{$action}}
                        Sheet
                    </div>

                    <div class="panel-body">
                        {{--Alerts Part--}}
                        @include('components.alert')

                        {{--Name--}}
                        <form class="form-horizontal add-edit-sheet-form" role="form" method="POST"
                              action="{{ url($url) }}">
                            {{ csrf_field() }}
                            <input type="hidden" id="problems-ids-hidden" name="problems"/>

                            {{--Name--}}
                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }} has-feedback">
                                <label for="name" class="col-md-2 control-label">Name</label>

                                <div class="col-md-10">
                                    <input id="name" type="text" class="form-control" name="name"
                                           value="{{ ($sheetName)?$sheetName:old('name') }}" placeholder="Name" required
                                           autofocus>

                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{--Problems--}}
                            <div class="add-edit-contest-problems-wrapper form-group{{ $errors->has('problems') ? ' has-error' : '' }} has-feedback">
                                <div class="row col-md-12">
                                    <label for="problems" class="control-label text-center">Problems</label>

                                    @include("contests.contest_views.add_edit_filter")
                                    @include("problems.table")
                                    @if ($errors->has('problems'))
                                        <span class="help-block text-left">
                                            <strong>{{ $errors->first('problems') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12 text-center">
                                    <button onclick="app.moveProblemsIDsSessionDataToHiddenField()" type="submit"
                                            class="btn btn-primary">
                                        {{$action}}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <span class="page-distinguishing-element" id="add-edit-sheet-page-hidden-element"
          data-selected-tags="{{($selected_tags)?$selected_tags:''}}"
          data-selected-judges="{{($selected_judges)?$selected_judges:''}}"
          @if(isset($sheet))
          data-name="{{$sheetName}}"
          data-problems="{{$sheet->problems()->pluck(\App\Utilities\Constants::FLD_PROBLEMS_ID)}}"
            @endif
    ></span>

@endsection
