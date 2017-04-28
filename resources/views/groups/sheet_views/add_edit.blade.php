@extends('layouts.app')

@section('content')
    <div class="container">
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
                    <div class="form-group">
                        <label for="name" class="col-md-2 control-label">Name</label>

                        <div class="col-md-10">
                            <input id="name" type="text" class="form-control" name="name"
                                   value="{{ isset($sheetName)?$sheetName:old('name') }}" placeholder="Name"
                                   required
                                   autofocus>

                        </div>
                    </div>

                    {{--Problems--}}
                    @include('components.problems_selector')

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
    <span class="page-distinguishing-element" id="add-edit-sheet-page-hidden-element"
          data-selected-tags="{{($selected_tags)?$selected_tags:''}}"
          data-selected-judges="{{($selected_judges)?$selected_judges:''}}"
          @if(isset($sheet))
          data-name="{{$sheetName}}"
          data-problems="{{$sheet->problems()->pluck(\App\Utilities\Constants::FLD_PROBLEMS_ID)}}"
            @endif
    ></span>

@endsection
