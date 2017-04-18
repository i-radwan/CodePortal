<div>
    {{--Organsisers AutoComplete--}}
    <div class="search-wrapper">
        <input id="organisers_auto" type="text" class="organisersAuto search-box" placeholder="Mention Organisers"
               data-organisers-path="{{route('contest/add/organisers_auto_complete')}}"
               data-old-tags="{{(session(Constants::CONTESTS_MENTIONED_ORGANISERS))?implode(",", session(Constants::CONTESTS_MENTIONED_ORGANISERS)):''}}"
               data-organisers-sync-path="{{Request::url()}}/Organisers_sync"
               data-organisers-token="{{csrf_token()}}"
               autocomplete="off" onkeypress="return event.keyCode != 13;"/>
        <button class="close-icon" type="reset"></button>
    </div>
    <div class="container">
        <ul id="organisers_list" class="tags-list">
            {{--Adding Previously Checked $Organisers--}}
            @if( isset($mOrganisers) )
                @foreach( $mOrganisers as $organiser)
                    <li name="organisers[]" value="{{$organiser}}">
                        <button class="organiser-close-icon "></button>{{$organiser}} </li>
                @endforeach
            @endif
        </ul>
    </div>
</div>

