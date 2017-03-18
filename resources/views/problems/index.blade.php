@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">

                    <div class="panel-heading">Problems</div>

                    <div class="panel-body">
                        @include('problems.table')
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">Filters</div>
                    <div class="panel-body">
                        @include('problems.filters')
                    </div>
                </div>
                @if(isset($data->tags) && count($data->tags) > 0)
                    <div class="panel panel-default">
                        <div class="panel-heading">Tags</div>
                        <div class="panel-body">
                            @include('problems.tags')
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection