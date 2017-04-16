{{--Errors--}}
@if(count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{--Normal Messages--}}
@if(Session::has('messages') && Session::get('messages') > 0)
    <div class="alert alert-success">
        <ul>
            @foreach (Session::get('messages') as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif
