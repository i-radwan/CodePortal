<form action="{{ $url }}" method="POST">

    {{--Hidden Fields--}}
    {{ csrf_field() }}
    {{ method_field( $method ) }}

    <button
            @if($confirm)
            onclick="return confirm({{ $confirmMsg }});"
            @endif
            type="submit" class="{{ $btnClasses }}"
            id="{{ $btnIDs }}">

        {{ $btnTxt }}

    </button>
</form>