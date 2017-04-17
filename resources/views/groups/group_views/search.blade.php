<form class="form-group has-feedback margin-30px group-search-box" role="form" method="GET" action="{{ Request::url() }}">
    <div class="input-group input-group-lg">
        <input type="text" class="form-control" value="{{ Request::get('name') }}" id="name" name="name" placeholder="Group name..." required>

        <span class="input-group-btn">
        <button type="submit" class="btn btn-primary">Search</button>
        </span>
    </div>
</form>