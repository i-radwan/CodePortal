<div class="panel panel-default">
    <div class="panel-heading">Tags</div>
    <div class="panel-body">

        @foreach ($tags as $tag)
            @php
                $tagName = $tag[\App\Utilities\Constants::FLD_TAGS_NAME];
                $urlQuery = http_build_query([\App\Utilities\Constants::URL_QUERY_TAG_KEY => $tagName]);
                $url = Request::url() . '?' . $urlQuery;
            @endphp

            <a href="{{ $url }}">
                <span class="badge problems-tag-badge">
                    {{ $tagName }}
                </span>
            </a>
        @endforeach
    </div>
</div>