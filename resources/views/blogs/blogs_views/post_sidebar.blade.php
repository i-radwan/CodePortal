<div class="col-md-4 post-filters">

    {{--New Post Button--}}
    <div class="text-right new-post-link">
        <a href="{{ route(\App\Utilities\Constants::ROUTES_BLOGS_POST_CREATE) }}">
            <button class="btn btn-primary">New Post</button>
        </a>
    </div>

    {{--Blog Search--}}
    <div class="well">

        <form action="/blogs" method="get" role="form">

            <h4>Blog Search</h4>

            <div class="input-group">
                {{--Search word input--}}
                <input name="q" type="text" class="form-control" value="{{ isset($q) ? $q: "" }}">

                {{--Search submit button--}}
                <span class="input-group-btn">
                    <button class="btn btn-default" type="submit">
                        <span class="glyphicon glyphicon-search"></span>
                    </button>
                </span>
            </div>
        </form>
    </div>

    {{--Top Contributors--}}
    @if(isset($topContributors))
        <div class="well">
            <h4>Top Contributors</h4>

            <hr/>

            {{--Contributors List--}}
            @foreach( $topContributors as $userName => $contributions)
                {{--Username profile link--}}
                <a target="_blank" href="{{ route(\App\Utilities\Constants::ROUTES_PROFILE, $userName) }}">
                    {{ $userName }}
                </a>

                @if(!$loop->last)
                    ,
                @endif
            @endforeach
        </div>
    @endif
</div>
