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

        {{--Delete button--}}
        <form action="{{ url('teams/' . $team->id) }}"
              method="POST">
            {{ method_field('DELETE') }}
            {{ csrf_field() }}

            <button onclick="return confirm('Are you sure want to delete the team?\nThis cannot be undone')"
                    type="submit"
                    class="btn btn-link text-dark pull-right margin-5px" id="testing-delete-team-{{ $team->id }}">
                Delete
            </button>
        </form>
    @elseif($isInvited)
        {{--Accept invitation button--}}
        <form action="{{ url('teams/' . $team->id . '/invitations/accept') }}"
              method="POST">
            {{ method_field('PUT') }}
            {{ csrf_field() }}

            <button type="submit" class="btn btn-link text-dark pull-right margin-5px"
                    id="testing-accept-team-{{ $team->id }}">
                Accept
            </button>
        </form>

        {{--Reject invitation button--}}
        <form action="{{ url('teams/' . $team->id . '/invitations/reject') }}"
              method="POST">
            {{ method_field('PUT') }}
            {{ csrf_field() }}

            <button type="submit" class="btn btn-link text-dark pull-right margin-5px"
                    id="testing-reject-team-{{ $team->id }}">
                Reject
            </button>
        </form>
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
                        {{--Remove member button--}}
                        <form action="{{ url('teams/' . $team->id . '/remove/' . $member->id) }}"
                              method="POST">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}

                            <button onclick="return confirm('Are you sure want to remove {{ $member->username }} from the team?')"
                                    type="submit"
                                    class="btn-link text-dark"
                                    id="testing-remove-member-team-{{ $team->id }}-{{ $member->username }}">
                                Remove
                            </button>
                        </form>
                    </th>
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
                        {{--Cancel invitation button--}}
                        <form action="{{ url('teams/' . $team->id . '/invitations/cancel/' . $user->id)}}"
                              method="POST">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}

                            <button onclick="return confirm('Are you sure want to cancel the invitation to {{ $member->username }}?')"
                                    type="submit"
                                    class="btn-link text-dark"
                                    id="testing-cancel-invitation-{{ $team->id }}-{{ $user->id }}">
                                Cancel Invitation
                            </button>
                        </form>
                    </th>
                @endif
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@if($isMember)
    @include('teams.invite')
@endif
