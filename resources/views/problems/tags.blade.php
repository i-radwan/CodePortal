<div class="panel panel-default">
    <div class="panel-heading">Tags</div>
    <div class="panel-body">

        @foreach ($tags as $tag)
            <a href="{{ Request::url() . '?' . http_build_query([Constants::URL_QUERY_TAG_KEY => $tag[\App\Utilities\Constants::FLD_TAGS_NAME]]) }}">
                <span class="badge problems-tag-badge">
                    {{ $tag[\App\Utilities\Constants::FLD_TAGS_NAME] }}
                </span>
            </a>
        @endforeach
    </div>
</div>