<form action="{{ $url }}" method="POST" {{ (isset($halfWidth) && $halfWidth) ? 'class=action':'' }}>

    {{--Hidden Fields--}}
    {{ csrf_field() }}
    {{ method_field($method) }}

    <button type="submit"
            class="{{ $btnClasses }}"
            id="{{ $btnIDs }}"
            @if($confirm)
                onclick="return confirm({{ $confirmMsg }});"
            @endif>
        {{ $btnTxt }}
    </button>
</form>