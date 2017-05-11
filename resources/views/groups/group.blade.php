{{--define some variables--}}
@php
    use App\Utilities\Constants;

    $groupID = $group[Constants::FLD_GROUPS_ID];
    $groupName = $group[Constants::FLD_GROUPS_NAME];
    $ownerUsername = $group->owner[Constants::FLD_USERS_USERNAME];

    $isOwner = ((Auth::check()) ? (Auth::user()->owningGroups()->find($groupID) != null) : false);
    $isOwnerOrAdmin = (Auth::check()) ? (\Gate::forUser(Auth::user())->allows('owner-admin-group', $group)) : false;
    $isMember = ((Auth::check()) ? (Auth::user()->joiningGroups()->find($groupID) != null) : false);
    $userSentRequest = (Auth::check() && !$isMember && Auth::user()->seekingJoinGroups()->find($groupID));
    $isGroup = true;

@endphp

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="panel panel-default">

            {{--Group leave/delete/join links--}}
            @if($isOwner)
                {{--Delete Form--}}
                @include('components.action_form', ['url' => route(\App\Utilities\Constants::ROUTES_GROUPS_DELETE, $groupID), 'method' => 'DELETE', 'confirm' => true, 'confirmMsg' => "'Are you sure want to delete this group? This action cannot be undone!'", 'btnIDs' => "", 'btnClasses' => 'btn btn-link text-dark pull-right margin-5px', 'btnTxt' => 'Delete'])

                {{--Edit Link--}}
                <a href="{{ route(\App\Utilities\Constants::ROUTES_GROUPS_EDIT, $groupID) }}"
                   class="btn btn-link text-dark pull-right margin-5px">Edit</a>
            @endif


            @if($isMember)

                {{--Leave Form--}}
                @include('components.action_form', ['url' => rotue(\App\Utilities\Constants::ROUTES_GROUPS_LEAVE, $groupID), 'method' => 'PUT', 'confirm' => true, 'confirmMsg' => "'Are you sure want to leave the group?'", 'btnIDs' => "", 'btnClasses' => 'btn btn-link text-dark pull-right margin-5px', 'btnTxt' => 'Leave'])

            @elseif(!$isOwnerOrAdmin && !$isMember)

                {{--Join From--}}
                @if($userSentRequest)
                    {{--Request already sent--}}
                    <span class="btn btn-link text-dark pull-right margin-5px" disabled>Request Sent</span>
                @else
                    @include('components.action_form', ['url' => route(\App\Utilities\Constants::ROUTES_GROUPS_REQUEST_STORE, $groupID), 'method' => 'POST', 'confirm' => false, 'confirmMsg' => "", 'btnIDs' => "", 'btnClasses' => 'btn btn-link text-dark pull-right margin-5px', 'btnTxt' => 'Join'])
                @endif
            @endif

            <div class="panel-heading">{{ $groupName }} ::
                <small>
                    <a href="{{ route(\App\Utilities\Constants::ROUTES_PROFILE, $ownerUsername) }}">{{ $ownerUsername }}</a>
                </small>
            </div>

            <div class="panel-body">
                @if($isMember || $isOwnerOrAdmin)
                    {{--Alerts Part--}}
                    @include('components.alert')

                    {{--Tabs Section--}}
                    <div class="content-tabs card">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#members" aria-controls="members" role="tab" data-toggle="tab"
                                   id="testing-members-link">Members</a>
                            </li>
                            <li role="presentation">
                                <a href="#admins" aria-controls="admins" role="tab" data-toggle="tab"
                                   id="testing-admins-link">Admins</a>
                            </li>
                            <li role="presentation">
                                <a href="#contests" aria-controls="contests" role="tab" data-toggle="tab"
                                   id="testing-contests-link">Contests</a>
                            </li>
                            <li role="presentation">
                                <a href="#sheets" aria-controls="sheets" role="tab" data-toggle="tab"
                                   id="testing-sheets-link">Sheets</a>
                            </li>
                            @if($isOwnerOrAdmin)
                                <li role="presentation">
                                    <a href="#requests" aria-controls="requests" role="tab"
                                       data-toggle="tab" id="testing-requests-link">Requests
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
                                @if($isOwnerOrAdmin)
                                    @include('groups.group_views.invite')
                                @endif
                                @if(count($members))
                                    @include('groups.group_views.members')
                                @else
                                    <p class="margin-30px">No members!</p>
                                @endif
                            </div>

                            <div role="tabpanel" class="tab-pane horizontal-scroll" id="admins">
                                @if(count($admins))
                                    @include('groups.group_views.admins')
                                @else
                                    <p class="margin-30px">No admins!</p>
                                @endif
                            </div>

                            <div role="tabpanel" class="tab-pane" id="contests">
                                @if($isOwnerOrAdmin)
                                    <a href="{{ route(\App\Utilities\Constants::ROUTES_GROUPS_CONTEST_CREATE, $groupID) }}"
                                       class="btn-sm btn btn-primary pull-right new-sheet-link"
                                       id="testing-group-new-contest-link">New Contest</a>
                                @endif
                                <div class="text-center horizontal-scroll">
                                    @include('contests.contest_views.contests_table', ['contests' => $contests])
                                </div>
                            </div>

                            <div role="tabpanel" class="tab-pane" id="sheets">
                                @if($isOwnerOrAdmin)
                                    <a href="{{ route(\App\Utilities\Constants::ROUTES_GROUPS_SHEET_CREATE, $groupID) }}"
                                       class="btn-sm btn btn-primary pull-right new-sheet-link">New Sheet</a>
                                @endif
                                @if(count($sheets))
                                    @include('groups.group_views.sheets')
                                @else
                                    <p class="margin-30px">No sheets!</p>
                                @endif
                            </div>

                            @if($isOwnerOrAdmin)
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