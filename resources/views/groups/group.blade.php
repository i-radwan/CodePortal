{{--define some variables--}}
@php
    $groupID = $data[Constants::SINGLE_GROUP_GROUP_KEY][Constants::SINGLE_GROUP_ID_KEY];
    $groupName = $data[Constants::SINGLE_GROUP_GROUP_KEY][Constants::SINGLE_GROUP_NAME_KEY];
    $ownerUsername = $data[Constants::SINGLE_GROUP_GROUP_KEY][Constants::SINGLE_GROUP_OWNER_KEY];

    $isOwner = $data[Constants::SINGLE_GROUP_EXTRA_KEY][Constants::SINGLE_GROUP_IS_USER_OWNER];
    $userSentRequest = $data[Constants::SINGLE_GROUP_EXTRA_KEY][Constants::SINGLE_GROUP_USER_SENT_REQUEST];
    $isMember = $data[Constants::SINGLE_GROUP_EXTRA_KEY][Constants::SINGLE_GROUP_IS_USER_MEMBER];
    $isGroup = true;

    $members = $data[Constants::SINGLE_GROUP_MEMBERS_KEY];
    $seekers = $data[Constants::SINGLE_GROUP_REQUESTS_KEY];
    $sheets = $data[Constants::SINGLE_GROUP_SHEETS_KEY];
    $contests = $data[Constants::SINGLE_GROUP_CONTESTS_KEY];
@endphp

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="panel panel-default">

            {{--Group leave/delete/join links--}}
            @if($isOwner)
                <form action="{{url('group/'.$groupID)}}"
                      method="post">{{method_field('DELETE')}}
                    {{csrf_field()}}
                    <button
                            onclick="return confirm('Are you sure want to delete the group?\nThis cannot be undone!')"
                            type="submit" class="btn btn-link text-dark pull-right margin-5px">Delete
                    </button>
                </form>
                <a href="{{url('group/edit/'.$groupID)}}" class="btn btn-link text-dark pull-right margin-5px">Edit</a>
            @endif
            @if($isMember)
                <form action="{{url('group/leave/'.$groupID)}}"
                      method="post">{{method_field('PUT')}}
                    {{csrf_field()}}
                    <button
                            onclick="return confirm('Are you sure want to leave the group?')"
                            type="submit" class="btn btn-link text-dark pull-right margin-5px">Leave
                    </button>
                </form>
            @elseif(!$isOwner && !$isMember)
                <form action="{{url('group/join/'.$groupID)}}"
                      method="post">{{method_field('POST')}}
                    {{csrf_field()}}
                    <button type="submit" class="btn btn-link text-dark pull-right margin-5px"
                            {{($userSentRequest)?'disabled':''}}>{{($userSentRequest)?'Request Sent':'Join'}}
                    </button>
                </form>
            @endif

            <div class="panel-heading">{{ $groupName }} ::
                <small><a href="{{url('profile/'.$ownerUsername)}}">{{$ownerUsername}}</a></small>
            </div>

            <div class="panel-body">
                @if($isMember || $isOwner)
                    {{--Alerts Part--}}
                    @include('components.alert')

                    {{--Tabs Section--}}
                    <div class="content-tabs card">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#members" aria-controls="members" role="tab" data-toggle="tab">Members</a>
                            </li>
                            <li role="presentation">
                                <a href="#contests" aria-controls="contests" role="tab" data-toggle="tab">Contests</a>
                            </li>
                            <li role="presentation">
                                <a href="#sheets" aria-controls="sheets" role="tab" data-toggle="tab">Sheets</a>
                            </li>
                            @if($isOwner)
                                <li role="presentation">
                                    <a href="#requests" aria-controls="requests" role="tab"
                                       data-toggle="tab">Requests
                                        @if(count($seekers))
                                            <span class="dark-red">•</span>
                                        @endif
                                    </a>
                                </li>
                            @endif
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">

                            <div role="tabpanel" class="tab-pane active horizontal-scroll" id="members">
                                @if($isOwner)
                                    @include('groups.group_views.invite')
                                @endif
                                @if(count($members))
                                    @include('groups.group_views.members')
                                @else
                                    <p class="margin-30px">No members!</p>
                                @endif
                            </div>

                            <div role="tabpanel" class="tab-pane" id="contests">
                                @if($isOwner)
                                    <a href="{{url('group/'.$groupID.'/contest/new')}}"
                                       class="btn-sm btn btn-primary pull-right new-sheet-link">New
                                        Contest</a>
                                @endif
                                <div class="text-center horizontal-scroll">
                                    @include('contests.contest_views.contests_table')
                                </div>
                            </div>

                            <div role="tabpanel" class="tab-pane" id="sheets">
                                @if($isOwner)
                                    <a href="{{url('sheet/new/'.$groupID)}}"
                                       class="btn-sm btn btn-primary pull-right new-sheet-link">New
                                        Sheet</a>
                                @endif
                                @if(count($sheets))
                                    @include('groups.group_views.sheets')
                                @else
                                    <p class="margin-30px">No sheets!</p>
                                @endif
                            </div>

                            @if($isOwner)
                                <div role="tabpanel" class="tab-pane horizontal-scroll" id="requests">
                                    @if(count($seekers))
                                        @include('groups.group_views.requests')
                                    @else
                                        <p class="margin-30px">No requests!</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <p class="margin-30px">Please join to see group details!</p>
                @endif
            </div>
        </div>
    </div>
    <span class="page-distinguishing-element" id="single-group-page-hidden-element"></span>
@endsection