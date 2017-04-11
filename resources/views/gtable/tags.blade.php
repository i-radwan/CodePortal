<div class="container tags-table-container">
    @foreach ( $data[Constants::FILTERS_TAGS] as $tag)
        <a href="{{Utilities::removeAppliedFilters(Request::fullUrl(),1)}}"> <span class="badge badge-default badge-pill problems-badge"> {{$tag->name}}</span></a>
    @endforeach
</div>
