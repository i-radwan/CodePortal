@extends('layouts.app')

@section('content')
    <?php dd($data); ?>
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default problems-panel">
                    <div class="panel-heading problems-panel-head">Problems</div>
                    <div class="panel-body problems-panel-body">
                        @if(count($data[Constants::TABLE_ROWS_KEY]) > 0)
                            @include('gtable.table')
                        @else
                            <p class="no-problems-msg">No
                                problems! {{(count($data->tagsIDs) > 0 || count($data->judgesIDs) > 0 ||strlen($data->q) > 0)?' please change the applied filters':''}}</p>
                        @endif
                    </div>
                </div>
            </div>
            {{--<div class="col-md-4">--}}
                {{--<div class="panel panel-default">--}}
                    {{--<div class="panel-heading">Filters</div>--}}
                    {{--<div class="panel-body">--}}
                        {{--@include('gtable.filters')--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--@if(isset($data->tags) && count($data->tags) > 0)--}}
                    {{--<div class="panel panel-default">--}}
                        {{--<div class="panel-heading">Tags</div>--}}
                        {{--<div class="panel-body">--}}
                            {{--@include('gtable.tags')--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--@endif--}}

            {{--</div>--}}
        </div>
    </div>
@endsection
