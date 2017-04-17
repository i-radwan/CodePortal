@extends('layouts.app')

@section('content')
    <div class="container">
        @include('problems.collapsible-filters')
        @include('problems.problems')
        @include('problems.tags')

        {{--Problems table--}}
        {{--<div class="col-md-8">--}}
            {{--@include('problems.problems')--}}
        {{--</div>--}}

        {{--Sidebar--}}
        {{--<div class="col-md-4">--}}
            {{--@include('problems.filters')--}}
            {{--@include('problems.tags')--}}
        {{--</div>--}}
    </div>
@endsection
