@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="panel panel-default groups-panel">

            {{--New--}}
            <a href="{{ url('/group/new') }}">
                <span class="btn btn-link text-dark pull-right margin-5px">New</span>
            </a>

            {{--Search Groups Link--}}
            <span onclick="$('.group-search-box').slideToggle();"
                  class="btn btn-link text-dark pull-right margin-5px group-search-icon"><i
                        class="fa fa-search"></i></span>

            {{--Clear button to clear search filters--}}
            @if(Request::has('name'))
                <a href="{{ url('groups') }}"
                   class="btn btn-link text-dark pull-right margin-5px">Clear</a>
            @endif


            <div class="panel-heading groups-panel-heading">Groups</div>
            <div class="panel-body groups-panel-body horizontal-scroll">

                {{--Search Section--}}
                @include('groups.group_views.search')

                @if($groups->count())
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center group-table-name-th">Name</th>
                            <th class="text-center">Owner</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($groups as $group)
                            @php
                                $groupID = $group[\App\Utilities\Constants::FLD_GROUPS_ID];
                                $groupName = $group[\App\Utilities\Constants::FLD_GROUPS_NAME];
                                $groupOwnerUsername = $group->owner[\App\Utilities\Constants::FLD_USERS_USERNAME];
                            @endphp
                            <tr>

                                {{--Group ID--}}
                                <td>{{ $groupID }}</td>

                                {{--Group name and link (for users only)--}}
                                <td>

                                    {{-- Only singed in users can see group details --}}
                                    @if(Auth::check())
                                        <a href="{{ url('group/' . $groupID) }}">
                                            {{ $groupName }}
                                        </a>
                                    @else
                                        {{ $groupName }}
                                    @endif

                                </td>

                                {{--Owner Username--}}
                                <td>
                                    <a href="{{ route(\App\Utilities\Constants::ROUTES_PROFILE, $groupOwnerUsername) }}">
                                        {{ $groupOwnerUsername }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>


                    {{--Pagination--}}
                    {{ $groups->appends(Request::all())->render() }}

                @else
                    <p class="margin-30px">No groups!</p>
                @endif
            </div>
        </div>
    </div>

    <span class="page-distinguishing-element" id="groups-page-hidden-element"></span>
@endsection