<div class="panel panel-default problems-filters-panel">
    <div class="panel-body">
        {{--Judges checkboxes--}}
        <div>
            Online Judges:
            @foreach ($judges as $judge)
                <div class="checkbox">
                    <label>
                        <input class="judgeState" type="checkbox" {{ in_array($judge->id, $cJudges) ? 'checked' : '' }}
                        value="{{ $judge->id }}">
                        {{ $judge->name }}
                    </label>
                </div>
            @endforeach
        </div>
        <hr>
        <div>
            {{--Tags AutoComplete--}}
            Tags:
            <div class="search-wrapper">
                <input id="tagsAuto" type="text" class="tagsAuto search-box" placeholder="Enter Tag" autocomplete="off"
                       onkeypress="return event.keyCode != 13;"
                       data-tags-path="{{route('contest/add/tags_auto_complete')}}"
                       data-old-tags="{{(session(Constants::CONTESTS_PROBLEMS_FILTERS))?implode(",", session(Constants::CONTESTS_PROBLEMS_FILTERS)[\App\Utilities\Constants::CONTESTS_CHECKED_TAGS]):''}}"/>
                <button class="close-icon" type="reset"></button>
            </div>
            <div class="container">
                <ul id="tagsList" class="tags-list" name="tags[]">
                    {{--Adding Previously Checked $tags--}}
                    @if( isset($cTags) )
                        @foreach( $cTags as $tag)
                            <li name="tags[]" value="{{$tag}}">
                                <button class="tags-close-icon "></button>{{$tag}} </li>
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>
        {{--Apply filters & Clear buttons--}}
        <p>
            <input class="btn btn-default" value="Apply Filters"
                   onclick="applyFilters('{{Request::url()}}/Tags_judges_filters_sync', '{{csrf_token()}}')"/>
            <a href="{{ Request::url() }}" class="btn btn-link text-dark pull-right contest_clear_problems_filters"
               id="clearTableLink">Clear</a>
        </p>
    </div>
</div>
