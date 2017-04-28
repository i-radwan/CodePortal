<div class="panel panel-default problems-panel">
    <div class="panel-heading problems-panel-heading">Problems</div>
    <div class="panel-body problems-panel-body">
        @if($problems->count())
            @include('problems.table')
        @else
            <h4 class="margin-30px">
                No problems!
                @if(count(Request::get(\App\Utilities\Constants::URL_QUERY_JUDGES_KEY)) || count(Request::get(\App\Utilities\Constants::URL_QUERY_TAG_KEY)) || strlen(Request::get(Constants::URL_QUERY_SEARCH_KEY)))
                    <br/>
                    please change the applied filters
                @endif
            </h4>
        @endif
    </div>
</div>