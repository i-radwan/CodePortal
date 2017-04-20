@extends('layouts.app')

@section('content')
    <div class="container">
        @include('problems.collapsible-filters')
        @include('problems.problems')
        @include('problems.tags')

    </div>
    <span class="page-distinguishing-element" id="problems-page-hidden-element"></span>

@endsection
