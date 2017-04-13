<div class="container tags-table-container">

    @foreach ($tags as $tag)
        <a href="{{ Request::url() . '?' . http_build_query(['tag' => $tag->id]) }}">
            <span class="badge problems-badge">
                {{ $tag->name }}
            </span>
        </a>
    @endforeach
</div>
