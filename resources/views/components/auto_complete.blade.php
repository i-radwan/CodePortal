{{--$itemsType : organisers, tags, invitees--}}
<div>

    {{--AutoComplete--}}
    <div id="custom-search-input">
        <div class="input-group autocomplete-input-group">

            {{--Hidden Field--}}
            <input type="hidden" id="{{ $hiddenID }}"
                   name="{{ $hiddenName }}">

            {{--Auto-complete field--}}
            <input id="{{ $itemsType }}-auto" type="text" class="form-control search-box"
                   placeholder="{{ $itemName }} name..."
                   data-{{ $itemsType }}-path="{{ $itemsLink }}"
                   onkeypress="return event.keyCode != 13;"
                    autocomplete="off">

        </div>
    </div>

    {{--Selected List--}}
    <div id="{{ $itemsType }}-list" class="autocomplete-list">

    </div>
</div>
