@php
    $authUser = Auth::user();
    $isMember = Gate::allows('member-team', $team);
    $isInvited = Gate::allows('invitee-team', $team);
@endphp

<div class="panel panel-default" id="testing-team-panel-{{ $team->id }}">
    {{--Check if authorized--}}
    @if($isMember)

        {{--Edit button--}}
        <a href="{{ url('teams/' . $team->id . '/edit') }}"
           class="btn btn-link text-dark pull-right margin-5px" id="testing-edit-team-{{ $team->id }}">
            Edit
        </a>

        {{-- Delete Form --}}
        @include('components.action_form', ['url' => url('teams/' . $team->id), 'method' => 'DELETE', 'confirm' => true, 'confirmMsg' => "'Are you sure want to delete this team? This action cannot be undone!'", 'btnIDs' => "testing-delete-team-$team->id", 'btnClasses' => 'btn btn-link text-dark pull-right margin-5px', 'btnTxt' => 'Delete'])

    @elseif($isInvited)

        {{--Accept invitation Form--}}
        @include('components.action_form', ['url' => url('teams/' . $team->id . '/invitations/accept'), 'method' => 'PUT', 'confirm' => false, 'confirmMsg' => "", 'btnIDs' => "testing-accept-team-$team->id", 'btnClasses' => 'btn btn-link text-dark pull-right margin-5px', 'btnTxt' => 'Accept'])

        {{--Reject invitation Form--}}
        @include('components.action_form', ['url' => url('teams/' . $team->id . '/invitations/reject'), 'method' => 'PUT', 'confirm' => true, 'confirmMsg' => "'Are you sure?'", 'btnIDs' => "testing-accept-team-$team->id", 'btnClasses' => 'btn btn-link text-dark pull-right margin-5px', 'btnTxt' => 'Accept'])

    @endif

    <div class="panel-heading">{{ $team->name }}</div>

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
            <tr>
                {{--Username--}}
                <td><a href="{{ url('profile/' . $member->username) }}">{{ $member->username }}</a></td>

                {{--E-Mail--}}
                <td>{{ $member->email }}</td>

                {{--User country--}}
                <td>{{ $member->country }}</td>

                @if($isMember)
                    <td>
                        {{--Remove member Form--}}
                        @include('components.action_form', ['url' => url('teams/' . $team->id . '/remove/' . $member->id), 'method' => 'DELETE', 'confirm' => true, 'confirmMsg' => "'Are you sure want to remove $member->username from the team?'", 'btnIDs' => "testing-remove-member-team-$team->id-$member->username", 'btnClasses' => 'btn-link text-dark', 'btnTxt' => 'Remove'])
                    </td>
                @endif
            </tr>
        @endforeach

        @foreach($team->invitedUsers()->get() as $user)
            <tr>
                {{--Username--}}
                <td>
                    <a href="{{ url('profile/' . $user->username) }}">{{ $user->username }}</a>
                    <div class="small">* Pending user response</div>
                </td>

                {{--E-Mail--}}
                <td>{{ $user->email }}</td>

                {{--User country--}}
                <td>{{ $user->country }}</td>

                @if($isMember)
                    <td>
                        {{--Cancel invitation form--}}
                        @include('components.action_form', ['url' => url('teams/' . $team->id . '/invitations/cancel/' . $user->id), 'method' => 'DELETE', 'confirm' => true, 'confirmMsg' => "'Are you sure want to cancel the invitation to $member->username ?'", 'btnIDs' => "testing-cancel-invitation-$team->id-$user->id", 'btnClasses' => 'btn-link text-dark', 'btnTxt' => 'Cancel Invitation'])
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
