@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="panel panel-default groups-panel">

            {{--New--}}
            <a href="{{ route(\App\Utilities\Constants::ROUTES_GROUPS_CREATE) }}">
                <span class="btn btn-link text-dark pull-right margin-5px">New</span>
            </a>

            {{--Search Groups Link--}}
            <span onclick="$('.group-search-box').slideToggle();"
                  class="btn btn-link text-dark pull-right margin-5px group-search-icon"><i
                        class="fa fa-search"></i></span>

            {{--Clear button to clear search filters--}}
            @if(Request::has('name'))
                <a href="{{ route(\App\Utilities\Constants::ROUTES_GROUPS_INDEX) }}"
                   class="btn btn-link text-dark pull-right margin-5px">Clear</a>
            @endif


            <div class="panel-heading groups-panel-heading">Groups</div>
            <div class="panel-body groups-panel-body horizontal-scroll">

                {{--Search Section--}}
                @include('groups.group_views.search')

                @include('groups.groups_table')

            </div>
        </div>
    </div>

    <span class="page-distinguishing-element" id="groups-page-hidden-element"></span>
@endsection