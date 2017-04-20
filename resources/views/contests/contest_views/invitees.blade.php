<div>
    {{--Invitees AutoComplete--}}
    <div id="custom-search-input">
        <div class="input-group autocomplete-input-group">
            <input id="invitees-auto" type="text" class="form-control invitees-auto search-box"
                   placeholder="Username..."
                   onkeypress="return event.keyCode != 13;"
                   data-invitees-path="{{url('contest/add/invitees_auto_complete')}}"
                   autocomplete="off">

        </div>
    </div>
    <div id="invitees-list" class="autocomplete-list">

    </div>

</div>

