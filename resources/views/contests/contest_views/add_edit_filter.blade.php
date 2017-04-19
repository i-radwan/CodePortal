<div class="panel panel-default problems-filters-panel">
    <div class="panel-body">
        {{--Judges checkboxes--}}
        <div>
            Online Judges:
            @foreach ($judges as $judge)
                <div class="checkbox">
                    <label>
                        <input class="judgeState" type="checkbox"
                               {{ in_array($judge->id, $judges->toArray()) ? 'checked' : '' }}
                               value="{{ $judge->id }}"
                               id="judge-checkbox-{{ $judge->id }}"
                               onchange="syncDataWithSession(judgesSessionKey, '{{ $judge->id }}', true)">
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
                       data-tags-path="{{url('contest/add/tags_auto_complete')}}"/>
                <button class="close-icon" type="reset"></button>
            </div>
            <div class="container">
                <ul id="tagsList" class="tags-list" name="tags[]">

                </ul>
            </div>
        </div>
        {{--Apply filters & Clear buttons--}}
        <p>
            <input class="btn btn-default" value="Apply Filters"
                   onclick="applyFilters('{{Request::url()}}/tags_judges_filters_sync', '{{csrf_token()}}')"/>
            <a href="{{ Request::url() }}" class="btn btn-link text-dark pull-right contest_clear_problems_filters"
               id="clearTableLink">Clear</a>
        </p>
    </div>
</div>
