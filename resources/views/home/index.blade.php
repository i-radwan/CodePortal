@extends('layouts.app')

@section('content')
    @include('home.cover')
    @include('home.features', $features)
    @include('home.quotes', $quotes)
    @include('home.sponsors', $sponsors)
@endsection

<span class="page-distinguishing-element" id="home-page-hidden-element"></span>
