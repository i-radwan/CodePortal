<!-- Comment -->
<div class="media">
    {{--To BE Later Use the User profile Photo--}}
    <a class="pull-left" href="#">
        <img class="media-object" src="http://placehold.it/64x64" alt="">
    </a>
    <div class="media-body">
        <h4 class="media-heading">
            {{--user Name--}}
            <a href='/profile/{{$comment["username"]}}' >{{$comment["username"]}}</a>

            {{--date and time--}}
            <small>{{\App\Utilities\Utilities::formatPastDateTime($comment[\App\Utilities\Constants::FLD_COMMENTS_CREATED_AT])}}</small>
        </h4>
        {{--body--}}
        <div class="well">
        <p class = "comment_body">{{$comment[\App\Utilities\Constants::FLD_COMMENTS_BODY]}}</p>
        </div>
        {{--Vote--}}
        &nbsp; &nbsp;<span>  <i class="fa fa-thumbs-o-down" aria-hidden="true"></i></span> {{$comment[\App\Utilities\Constants::FLD_COMMENTS_DOWN_VOTES]}} &nbsp; &nbsp; <i class="fa fa-thumbs-o-up" aria-hidden="true"></i> {{$comment[\App\Utilities\Constants::FLD_COMMENTS_UP_VOTES]}}
        {{--Replies--}}
        @if( isset($comment[\App\Utilities\Constants::COMMENTS_REPLIES]))
            @foreach($comment[\App\Utilities\Constants::COMMENTS_REPLIES] as $comment)
                @include("blogs.blogs_views.comment")
            @endforeach
        @endif
    </div>
</div>
