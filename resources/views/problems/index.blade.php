@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default problems-panel">
                    <div class="panel-heading problems-panel-head">Problems</div>
                    <div class="panel-body problems-panel-body">
                        @if($data[Constants::TABLE_ROWS_KEY])
                            @include('gtable.table')
                        @else
                            <p class="no-problems-msg">No
                                problems! {{(count($data[Constants::PREVIOUS_TABLE_FILTERS][Constants::APPLIED_FILTERS_JUDGES_IDS]) > 0 || count($data[Constants::PREVIOUS_TABLE_FILTERS][Constants::APPLIED_FILTERS_TAGS_IDS]) > 0 ||strlen($data[Constants::PREVIOUS_TABLE_FILTERS][Constants::APPLIED_FILTERS_SEARCH_STRING]) > 0)?' please change the applied filters':''}}</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">Filters</div>
                    <div class="panel-body">
                        @include('gtable.filters')
                    </div>
                </div>
                @if(isset($data[Constants::FILTERS_TAGS]) && count($data[Constants::FILTERS_TAGS]) > 0)
                    <div class="panel panel-default">
                        <div class="panel-heading">Tags</div>
                        <div class="panel-body">
                            @include('gtable.tags')
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection
