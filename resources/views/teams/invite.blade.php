<form class="form-group has-feedback" role="form" method="POST" action="{{ url('teams/' . $teamID . '/invite/') }}">
    {{ csrf_field() }}
    <div class="input-group input-group-lg">

        {{--Usernames hidden field--}}
        <input type="hidden" id="testing-team-username-{{ $teamID }}" name="username" required>

        {{--Auto complete usernames field--}}
        <input id="invitees-auto" type="text" class="form-control" onkeypress="return event.keyCode != 13;"
               data-invitees-path="{{url('teams/' . $teamID . '/invitees_auto_complete')}}"
               autocomplete="off" placeholder="Username...">

        {{--Invite Button--}}
        <span class="input-group-btn">
            <button type="submit" class="btn btn-primary"
                    id="testing-team-send-{{ $teamID }}"
                    onclick="app.moveInviteesFromSessionToField('testing-team-username-{{ $teamID }}', app.inviteesSessionKey, true)">Invite</button>
        </span>
    </div>

    {{--Auto complete selected usernames list--}}
    <div id="invitees-list" class="autocomplete-list">

    </div>
</form>