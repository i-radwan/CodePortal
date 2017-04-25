<!-- Comment -->
<div class="media">
    {{--To BE Later Use the User profile Photo--}}
    <a class="pull-left" href="#">
        <img class="media-object" src="http://placehold.it/64x64" alt="">
    </a>
    <div class="media-body">

        <h4 class="media-heading">
            {{--user Name--}}
            <a href='/profile/{{$comment["username"]}}'>{{$comment["username"]}}</a>

            {{--date and time--}}
            <small>{{\App\Utilities\Utilities::formatPastDateTime($comment[\App\Utilities\Constants::FLD_COMMENTS_CREATED_AT])}}</small>
        </h4>

        {{--body--}}
        <div class="well">
            <p class="comment_body">{{$comment[\App\Utilities\Constants::FLD_COMMENTS_BODY]}}</p>
        </div>

        {{--Vote--}}
        <span>
        <a href="{{ $comment_unlike_url }}/{{$comment[\App\Utilities\Constants::FLD_COMMENTS_COMMENT_ID]}}"
           id="comment-{{ $comment[\App\Utilities\Constants::FLD_COMMENTS_COMMENT_ID] }}-down-vote-icon">
            @if(!isset($comment["user_vote"]) or $comment["user_vote"] != 0)
                <i class="fa fa-thumbs-o-down" aria-hidden="true"> </i>
            @else
                <i class="fa fa-thumbs-down" aria-hidden="true"></i>
            @endif
        </a>
            <span id="comment-{{ $comment[\App\Utilities\Constants::FLD_COMMENTS_COMMENT_ID] }}-down-votes-count">{{$comment[\App\Utilities\Constants::FLD_COMMENTS_DOWN_VOTES]}}</span> &nbsp;
        <a href="{{$comment_like_url}}/{{$comment[\App\Utilities\Constants::FLD_COMMENTS_COMMENT_ID]}}"
           id="comment-{{ $comment[\App\Utilities\Constants::FLD_COMMENTS_COMMENT_ID] }}-up-vote-icon">
            @if(!isset($comment["user_vote"]) or $comment["user_vote"] != 1)
                <i class="fa fa-thumbs-o-up"aria-hidden="true"> </i>
            @else
                <i class="fa fa-thumbs-up"aria-hidden="true"> </i>
            @endif
        </a>
            <span id="comment-{{ $comment[\App\Utilities\Constants::FLD_COMMENTS_COMMENT_ID] }}-up-votes-count">{{$comment[\App\Utilities\Constants::FLD_COMMENTS_UP_VOTES]}}</span>
    </span>
        {{--Replies--}}
        @if( isset($comment[\App\Utilities\Constants::COMMENTS_REPLIES]))
            @foreach($comment[\App\Utilities\Constants::COMMENTS_REPLIES] as $comment)
                @include("blogs.blogs_views.comment")
            @endforeach
        @endif
    </div>
</div>
