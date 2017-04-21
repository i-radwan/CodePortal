<div>
    {{--Organsisers AutoComplete--}}
    <div id="custom-search-input">
        <div class="input-group autocomplete-input-group">
            <input id="organisers-auto" type="text" class="form-control search-box"
                   placeholder="Organiser name..."
                   onkeypress="return event.keyCode != 13;"
                   data-organisers-path="{{url('contest/add/organisers_auto_complete')}}"
                   autocomplete="off">

        </div>
    </div>
    <div id="organisers-list" class="autocomplete-list">

    </div>

</div>

