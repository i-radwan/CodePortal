@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            {{--Problems table--}}
            <div class="col-md-8">
                <div class="panel panel-default problems-panel">
                    <div class="panel-heading problems-panel-head">Problems</div>
                    <div class="panel-body problems-panel-body">
                        @if($problems->count())
                            @include('problems.table')
                        @else
                            <p class="no-problems-msg">
                                No problems!
                                @if(count(Request::get('judges')) || count(Request::get('tags')) || strlen(Request::get('q')))
                                    <br/>
                                    please change the applied filters
                                @endif
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {{--Filter bar--}}
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">Filters</div>
                    <div class="panel-body">
                        @include('problems.filters')
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">Tags</div>
                    <div class="panel-body">
                        @include('problems.tags')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
