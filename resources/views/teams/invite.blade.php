<form class="form" method="POST" action="{{ url('teams/' . $team->id . '/invite/') }}">
    {{ csrf_field() }}

    <div class="form-group">
        <input type="text" name="username" class="form-control" placeholder="Username...">
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary">Send Invitation</button>
    </div>
</form>