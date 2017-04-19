<div>
    {{--Organsisers AutoComplete--}}
    <div class="search-wrapper">
        <input id="organisers-auto" type="text" class="organisers-auto search-box"
               placeholder="Mention organisers..."
               data-organisers-path="{{url('contest/add/organisers_auto_complete')}}"
               autocomplete="off" onkeypress="return event.keyCode != 13;"/>
        <button class="close-icon" type="reset"></button>
    </div>
    <div class="container">
        <ul id="organisers-list" class="tags-list">
        </ul>
    </div>
</div>

