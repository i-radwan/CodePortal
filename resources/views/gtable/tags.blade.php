<div class="container tags-table-container">

    @foreach ( $data[Constants::FILTERS_TAGS] as $tag)
        <a href="{{Utilities::getURL(Constants::APPLIED_FILTERS_TAG_ID, $tag->id ,url()->current() ,Utilities::removeAppliedFilters(Request::fullUrl(),1))}}"> <span class="badge badge-default badge-pill problems-badge"> {{$tag->name}}</span></a>
    @endforeach
</div>
