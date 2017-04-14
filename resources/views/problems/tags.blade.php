<div class="panel panel-default">
    <div class="panel-heading">Tags</div>
    <div class="panel-body">

        @foreach ($tags as $tag)
            <a href="{{ Request::url() . '?' . http_build_query([Constants::URL_QUERY_TAG_KEY => $tag->id]) }}">
                <span class="badge problems-tag-badge">
                    {{ $tag->name }}
                </span>
            </a>
        @endforeach
    </div>
</div>