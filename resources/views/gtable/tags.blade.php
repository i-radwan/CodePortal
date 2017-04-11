<div class="container tags-table-container">
        {{--{{ TODO: NOT YET MADE GENERIC}}--}}
    @foreach ( $data[Constants::FILTERS_TAGS] as $tag)
        {{--TODOSAMRA: --}}{{--get the query then add the tags--}}
        <a href="/problems?tag={{$tag->id}}"> <span class="badge badge-default badge-pill problems-badge"> {{$tag->name}}</span></a>
    @endforeach
</div>
