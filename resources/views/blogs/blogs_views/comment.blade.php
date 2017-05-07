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
            <p class="comment-body">{{$comment[\App\Utilities\Constants::FLD_COMMENTS_BODY]}}</p>
        </div>

        {{--Add Comment Options--}}
        @include('blogs.blogs_views.comment_options')

        {{--Add Reply Form--}}
        @include('blogs.blogs_views.add_comment_form')

        {{--Show Comment Replies--}}
        @if( isset($comment[\App\Utilities\Constants::COMMENTS_REPLIES]))
            @foreach($comment[\App\Utilities\Constants::COMMENTS_REPLIES] as $comment)
                @include("blogs.blogs_views.comment")
            @endforeach

        @endif
    </div>
</div>

