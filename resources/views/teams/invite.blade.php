<form class="form" method="POST" action="{{ url('teams/' . $team->id . '/invite/') }}">
    {{ csrf_field() }}

    <div class="form-group">
        <input id="testing-team-username-{{ $team->id }}" type="text" name="username" class="form-control"
               placeholder="Username...">
    </div>

    <div class="form-group">
        <button id="testing-team-send-{{ $team->id }}" type="submit" class="btn btn-primary">Send Invitation</button>
    </div>
</form>