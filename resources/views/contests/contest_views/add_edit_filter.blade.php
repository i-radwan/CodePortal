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
                       data-tags-path="{{route('contest/add/tagsautocomplete')}}"
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
                   onclick="applyFilters('{{Request::url()}}/TagsJudgesFSync', '{{csrf_token()}}')"/>
            <a href="{{ Request::url() }}" class="btn btn-link text-dark pull-right contest_clear_problems_filters"
               id="clearTableLink">Clear</a>
        </p>
    </div>
</div>
{{--Problems Count--}}
{{--@if(isset($checkedRows))--}}
{{--<div class="panel panel-default problems-filters-panel">--}}
{{--<div class="panel-body">--}}
{{--<div class="container">--}}
{{--Problems Count: {{count($checkedRows)}}--}}
{{--</div>--}}
{{--</div>--}}
{{--<hr>--}}
{{--<div class="panel-body">--}}
{{--<div class="container">--}}
{{--Problems:--}}
{{--<ul>--}}
{{--@foreach( $checkedRows as $problemRow)--}}
{{--<li>{{($problemRow)}}</li>--}}
{{--@endforeach--}}
{{--</ul>--}}
{{--</div>--}}
{{--</div>--}}
{{--</div>--}}
{{--@endif--}}
