<!DOCTYPE html>
<html>
<body>
<div class="panel panel-default problems-filters-panel">
    <div class="panel-body">
            {{--Judges checkboxes--}}
            <div>
                <h5>Online Judges:</h5>
                @foreach ($judges as $judge)
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" {{ in_array($judge->id, Request::get(Constants::URL_QUERY_JUDGES_KEY, [])) ? 'checked' : '' }}
                            name="{{ Constants::URL_QUERY_JUDGES_KEY }}[]"
                                   value="{{ $judge->id }}">
                            {{ $judge->name }}
                        </label>
                    </div>
                @endforeach
            </div>
            <hr>
            <div>
                <h5>Tags:</h5>
                <div class="search-wrapper" >
                        <input id="tagsAuto" type="text"  class="typeahead search-box" placeholder="Enter Tag" autocomplete="off"/>
                        <button class="close-icon" type="reset"></button>
                </div>
                <div class="container">
                    <ul id="tagsList" name="tags[]">
                    </ul>
                </div>
            </div>
    </div>
</div>
</body>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.js"></script>
<script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script type="text/javascript" src="//codeorigin.jquery.com/ui/1.10.2/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>
{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.js"></script>--}}
<script type="text/javascript">
    var list = document.getElementById("tagsList");
    var path = "{{route('tagsautocomplete')}}";
    //Tags AutoComplete
    $('input.typeahead').typeahead({
            source: function( query, process){
                return $.get(path, { query: query}, function(data){
                   return process(data);
                   //you may make the source a hardcoded list
                })
            },
            updater:function (item) { //append in the unordered list
                //item = selected item
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
                    entry.appendChild(document.createTextNode(item.name));
                    list.appendChild(entry);
                }
                //forget to return 'item' to avoid reflecting it into input text box
                return ;
            }
        });
    //Organisers AutoComplete //TODO : later On
</script>

</html>

