<div class="panel panel-default problems-filters-panel">
    <div class="panel-body">
            {{--Judges checkboxes--}}
            <div>
                Online Judges:
                @foreach ($judges as $judge)
                    <div class="checkbox">
                        <label>
                            <input class="judgeState" type="checkbox" {{ in_array($judge->id, Request::get(Constants::CONTESTS_CHECKED_JUDGES, [])) ? 'checked' : '' }}
                            name="{{ Constants::URL_QUERY_JUDGES_KEY }}[]"
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
                    <input id="tagsAuto" type="text" class="tagsAuto search-box" placeholder="Enter Tag" autocomplete="off"/>
                    <button class="close-icon" type="reset"></button>
            </div>
            <div class="container">
                <ul id="tagsList" class = "tags-list" name="tags[]">
                    {{--Adding Previously Checked $tags--}}
                    @if( isset($cTags) )
                        @foreach( $cTags as $tag)
                            <li name = "tags[]" value = "{{$tag}}" ><button class="tags-close-icon "></button>{{$tag}} </li>
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>
        {{--Apply filters & Clear buttons--}}
        <p>
            <input  class="btn btn-default" value="Apply Filters" onclick="applyFilters()"/>
            <a href="{{ Request::url() }}" class="btn btn-link text-dark pull-right">Clear</a>
        </p>
    </div>

</div>

<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.js"></script>
<script type="text/javascript" src="//codeorigin.jquery.com/ui/1.10.2/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>

<script type="text/javascript" src="/js/contest.js" ></script>
<script type="text/javascript">
    //Tags AutoComplete parameters
    var tagsList = document.getElementById("tagsList");
    var tagsPath = "{{route('contest/add/tagsautocomplete')}}";
    $('input.tagsAuto').typeahead(autoComplete(tagsPath, tagsList, "tags[]",0));
    function applyFilters() {
        var filters = getCurrentFilters();
        $.ajax({
            url: "{{Request::url()}}/TagsJudgesFSync",
            type: 'POST',
            data: {
                _token: "{{csrf_token()}}",
                cProblemsFilters : filters,
            },
            success: function(data){
            }
        });
        location.reload();
    }
    //Wait for deletion key
    $(document).on('mousedown','.tags-close-icon', function(item) {
                        $(this).parent().remove();
    });
</script>


