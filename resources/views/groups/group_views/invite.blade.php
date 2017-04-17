<form class="form-group has-feedback" role="form" method="POST" action="{{url('group/member/invite/'.$groupID)}}">
    {{ csrf_field() }}
    <div class="input-group input-group-lg">
        {{--ToDo: replace with SAMIR'S autocomplete--}}
        <input type="text" class="form-control" id="username" name="username" placeholder="Username..." required>

        <span class="input-group-btn">
        <button type="submit" class="btn btn-primary">Invite</button>
        </span>
    </div>
</form>