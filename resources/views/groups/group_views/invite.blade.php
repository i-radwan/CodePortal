<form class="form-group" role="form" method="POST" action="{{ route(\App\Utilities\Constants::ROUTES_GROUPS_INVITATION_STORE, $groupID) }}">
    {{ csrf_field() }}
    <div class="input-group input-group-lg">

        {{--Usernames hidden field--}}
        <input type="hidden" id="usernames" name="usernames" required>

        {{--Auto complete usernames field--}}
        <input id="invitees-auto" type="text" class="form-control autocomplete-input" onkeypress="return event.keyCode != 13;"
               data-session-key="group_invitees_session_key"
               data-list-id="invitees-list"
               data-path="{{ route(\App\Utilities\Constants::ROUTES_GROUPS_INVITEES_AUTO_COMPLETE, $groupID) }}"
               autocomplete="off" placeholder="Username...">

        {{--Invite Button--}}
        <span class="input-group-btn">
            <button type="submit" class="btn btn-primary"
                    onclick="app.moveDataFromSessionToField('usernames', 'group_invitees_session_key', true)">Invite</button>
        </span>
    </div>

    {{--Auto complete selected usernames list--}}
    <div id="invitees-list" class="autocomplete-list">

    </div>
</form>