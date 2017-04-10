<div class="container tags-table-container">
    @foreach ( $data->tags as $tag)
        {{--TODOSAMRA: --}}{{--get the query then add the tags--}}
        <a href="/problems?tag={{$tag->id}}"> <span class="badge badge-default badge-pill problems-badge"> {{$tag->name}}</span></a>
    @endforeach
</div>
