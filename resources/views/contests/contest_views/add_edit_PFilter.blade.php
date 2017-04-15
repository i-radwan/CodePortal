<div class="panel panel-default problems-filters-panel">
    <div class="panel-body">
            {{--Judges checkboxes--}}
            <div>
                <h5>Online Judges:</h5>
                @foreach ($judges as $judge)
                    <div class="checkbox">
                        <label>
                            <input class="judgeState" type="checkbox" {{ in_array($judge->id, Request::get(Constants::URL_QUERY_JUDGES_KEY, [])) ? 'checked' : '' }}
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
            <h5>Tags:</h5>
            <div class="search-wrapper">
                {{--<form>--}}
                    <input id="tagsAuto" type="text" class="tagsAuto search-box" placeholder="Enter Tag" autocomplete="off"/>
                    <button class="close-icon" type="reset"></button>
                {{--</form>--}}
            </div>
            <div class="container">
                <ul id="tagsList" class = "tags-list" name="tags[]">
                    {{--Adding Previously Checked $tags--}}
                    @if( isset($tags) )
                        @foreach( $tags as $tag)
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
<script type="text/javascript" >
    var list = document.getElementById("tagsList");
    var path = "{{route('contest/add/tagsautocomplete')}}";
    //Tags AutoComplete
    $('input.tagsAuto').typeahead({
            source: function( query, process){
                return $.get(path, { query: query}, function(data){
                   return process(data);
                })
            },
            updater:function (item) {
                console.log(document.getElementsByName('{{Constants::URL_QUERY_JUDGES_KEY }}[]'));
                //Get the current values of list items in the unordered list 'tagsList'
                var currentItems = list.getElementsByTagName('li');
                //check if it's already included
                var notFound = true;
                for(var i = 0; i < currentItems.length; i++){
                    if( currentItems[i].textContent == item['name'] )
                        notFound = false;
                }
                if(notFound){
                    //Create a new list item li
                    var entry = document.createElement('li');
                    entry.setAttribute("name","tags[]");
                    entry.setAttribute("value",item.name);
                    //Add the item name and the delete button
                    var text = '<button class="tags-close-icon "></button>';
                    entry.innerHTML = text + item.name;
                    list.appendChild(entry);
                }
                //Wait for deletion key
                $(document).on('mousedown','.tags-close-icon', function(item) {
                        $(this).parent().remove();
                });
                //Don't return the item name back in order not to keep it in the text box field
                return ;
            }
        });
    function URL_add_parameter(url, param, value){
        var hash       = {};
        var parser     = document.createElement('a');

        parser.href    = url;

        var parameters = parser.search.split(/\?|&/);

        for(var i=0; i < parameters.length; i++) {
            if(!parameters[i])
                continue;

            var ary      = parameters[i].split('=');
            hash[ary[0]] = ary[1];
        }

        hash[param] = value;

        var list = [];
        Object.keys(hash).forEach(function (key) {
            list.push(key + '=' + hash[key]);
        });

        parser.search = '?' + list.join('&');
        return parser.href;
    }

    function getCurrentFilters(){
        //Reading Tags
        var currentItems = list.getElementsByTagName('li');
        var tags =[];
        for(var i = 0; i < currentItems.length; i++){
            tags[i] = currentItems[i].textContent;
        }
        //Reading Judges info
        var judges = [];
        var j = 0;
        var checkboxes = document.getElementsByClassName('judgeState');
        for(var i=0; checkboxes[i]; ++i){
            if(checkboxes[i].checked){
                judges[j] = checkboxes[i].value;
                j = j + 1;
            }
        }
        //Then you have now judges and tags
        return({'tags' : tags,'judges': judges });

    }
    function applyFilters() {
        console.log("Hey I am here, PLease do request here El7");
        console.log(getCurrentFilters());
        {{--$.ajax({--}}
            {{--url: "{{Request::url()}}/TagsJudgesFSync",--}}
            {{--type: 'POST',--}}
            {{--data: {--}}
                {{--_token: "{{csrf_token()}}",--}}
                {{--checkedRows : checkedRows,--}}
                {{--page : "{{Request::get('page') != null ? Request::get('page') : 1}}"--}}
            {{--},--}}
            {{--success: function(data){--}}
                {{--console.log(data);--}}
            {{--}--}}
        {{--});--}}
        {{--location.reload();--}}
    }
    //Organisers AutoComplete //TODO : later On
</script>


