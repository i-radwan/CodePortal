@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="panel panel-default groups-panel">
            <a href="{{ url('/group/new') }}">
                <span class="btn btn-link text-dark pull-right margin-5px">New</span>
            </a>
            <span onclick="$('.group-search-box').slideToggle();"
                  class="btn btn-link text-dark pull-right margin-5px group-search-icon"><i
                        class="fa fa-search"></i></span>
            @if(Request::has('name'))
                <a href="{{ url('groups') }}"
                   class="btn btn-link text-dark pull-right margin-5px">Clear</a>
            @endif
            <div class="panel-heading groups-panel-heading">Groups</div>
            <div class="panel-body groups-panel-body horizontal-scroll">
                @include('groups.group_views.search')

                @if(count($data[Constants::GROUPS_GROUPS_KEY]))
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center group-table-name-th">Name</th>
                            <th class="text-center">Owner</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data[Constants::GROUPS_GROUPS_KEY] as $group)
                            <tr>
                                <td>{{ $group->id }}</td>
                                <td>

                                    {{-- Only singed in users can see group details --}}
                                    @if(Auth::check())
                                        <a href="{{ url('group/' . $group->id) }}">
                                            {{ $group->name }}
                                        </a>
                                    @else
                                        {{ $group->name }}
                                    @endif

                                </td>
                                <td>
                                    <a href="{{ url('profile/' . $group->owner->username)}}">
                                        {{ $group->owner->username }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{--Pagination--}}
                    {{ $data[Constants::GROUPS_GROUPS_KEY]->appends(Request::all())->render() }}
                @else
                    <p class="margin-30px">No groups!</p>
                @endif
            </div>
        </div>
    </div>
@endsection