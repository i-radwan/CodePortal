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
                    <input id="tagsAuto" type="text" class="tagsAuto search-box" placeholder="Enter Tag" autocomplete="off" onkeypress="return event.keyCode != 13;"/>
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
            <a href="{{ Request::url() }}" class="btn btn-link text-dark pull-right contest_clear_problems_filters" id="clearTableLink" >Clear</a>
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
//        location.reload();
        document.getElementById("clearTableLink").click();
    }
    //Wait for deletion key
    $(document).on('mousedown','.tags-close-icon', function(item) {
                        $(this).parent().remove();
    });
    //get Current Entered Form Data
//    function getEnteredFormData() {
//        var conName = document.getElementById("name").value;
//        var conDate = document.getElementById("time").value;
//        var conDur = document.getElementById("duration").value;
//        var conVis = document.getElementById("private").value;
//        return {
//            name : conName,
//            date : conDate,
//            duration: conDur,
//            visibility : conVis
//        };
//    }
</script>


