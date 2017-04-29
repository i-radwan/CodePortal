@php
    $authUser = Auth::user();
    $isMember = Gate::allows('member-team', $team);
    $isInvited = Gate::allows('invitee-team', $team);
    $teamID = $team[\App\Utilities\Constants::FLD_TEAMS_ID];
    $teamName = $team[\App\Utilities\Constants::FLD_TEAMS_NAME];
@endphp

<div class="panel panel-default" id="testing-team-panel-{{ $teamID }}">
    {{--Check if authorized--}}
    @if($isMember)

        {{--Edit button--}}
        <a href="{{ route(\App\Utilities\Constants::ROUTES_TEAMS_EDIT, $teamID) }}"
           class="btn btn-link text-dark pull-right margin-5px" id="testing-edit-team-{{ $teamID }}">
            Edit
        </a>

        {{-- Delete Form --}}
        @include('components.action_form', ['url' => route(\App\Utilities\Constants::ROUTES_TEAMS_DELETE, $teamID), 'method' => 'DELETE', 'confirm' => true, 'confirmMsg' => "'Are you sure want to delete this team? This action cannot be undone!'", 'btnIDs' => "testing-delete-team-$teamID", 'btnClasses' => 'btn btn-link text-dark pull-right margin-5px', 'btnTxt' => 'Delete'])

    @elseif($isInvited)

        {{--Accept invitation Form--}}
        @include('components.action_form', ['url' => route(\App\Utilities\Constants::ROUTES_TEAMS_INVITATIONS_ACCEPT, $teamID), 'method' => 'PUT', 'confirm' => false, 'confirmMsg' => "", 'btnIDs' => "testing-accept-team-$teamID", 'btnClasses' => 'btn btn-link text-dark pull-right margin-5px', 'btnTxt' => 'Accept'])

        {{--Reject invitation Form--}}
        @include('components.action_form', ['url' => route(\App\Utilities\Constants::ROUTES_TEAMS_INVITATIONS_REJECT, $teamID), 'method' => 'PUT', 'confirm' => true, 'confirmMsg' => "'Are you sure?'", 'btnIDs' => "testing-accept-team-$teamID", 'btnClasses' => 'btn btn-link text-dark pull-right margin-5px', 'btnTxt' => 'Reject'])

    @endif

    <div class="panel-heading">{{ $teamName }}</div>

    <table class="table table-bordered table-hover text-center">
        <thead>
        <tr>
            <th class="text-center">Username</th>
            <th class="text-center">E-Mail</th>
            <th class="text-center">Country</th>

            @if($isMember)
                <th class="text-center">Actions</th>
            @endif
        </tr>
        </thead>

        <tbody>
        @foreach($team->members()->get() as $member)
            @php
                $memberUsername = $member[\App\Utilities\Constants::FLD_USERS_USERNAME];
                $memberEmail = $member[\App\Utilities\Constants::FLD_USERS_EMAIL];
                $memberID = $member[\App\Utilities\Constants::FLD_USERS_ID];
                $memberCountry = $member[\App\Utilities\Constants::FLD_USERS_COUNTRY];
            @endphp
            <tr>
                {{--Username--}}
                <td><a href="{{ route(\App\Utilities\Constants::ROUTES_PROFILE, $memberUsername) }}">{{ $memberUsername }}</a></td>

                {{--E-Mail--}}
                <td>{{ $memberEmail }}</td>

                {{--User country--}}
                <td>{{ $memberCountry }}</td>

                @if($isMember)
                    <td>
                        {{--Remove member Form--}}
                        @include('components.action_form', ['url' => route(\App\Utilities\Constants::ROUTES_TEAMS_MEMBERS_REMOVE, [$teamID, $memberUsername]), 'method' => 'DELETE', 'confirm' => true, 'confirmMsg' => "'Are you sure want to remove $memberUsername from the team?'", 'btnIDs' => "testing-remove-member-team-$teamID-$memberUsername", 'btnClasses' => 'btn-link text-dark', 'btnTxt' => 'Remove'])
                    </td>
                @endif
            </tr>
        @endforeach

        @foreach($team->invitedUsers()->get() as $user)
            @php
                 $inviteeUsername = $user[\App\Utilities\Constants::FLD_USERS_USERNAME];
                 $inviteeEmail = $user[\App\Utilities\Constants::FLD_USERS_EMAIL];
                 $inviteeID = $user[\App\Utilities\Constants::FLD_USERS_ID];
                 $inviteeCountry = $user[\App\Utilities\Constants::FLD_USERS_COUNTRY];
            @endphp
            <tr>
                {{--Username--}}
                <td>
                    <a href="{{ route(\App\Utilities\Constants::ROUTES_PROFILE, $inviteeUsername) }}">{{ $inviteeUsername }}</a>
                    <div class="small">* Pending user response</div>
                </td>

                {{--E-Mail--}}
                <td>{{ $inviteeEmail }}</td>

                {{--User country--}}
                <td>{{ $inviteeCountry }}</td>

                @if($isMember)
                    <td>
                        {{--Cancel invitation form--}}
                        @include('components.action_form', ['url' => route(\App\Utilities\Constants::ROUTES_TEAMS_INVITATIONS_CANCEL, [$teamID, $inviteeUsername]), 'method' => 'DELETE', 'confirm' => true, 'confirmMsg' => "'Are you sure want to cancel the invitation to $memberUsername ?'", 'btnIDs' => "testing-cancel-invitation-$teamID-$inviteeID", 'btnClasses' => 'btn-link text-dark', 'btnTxt' => 'Cancel Invitation'])
                    </td>
                @endif
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@if($isMember)
    @include('teams.invite')
@endif
