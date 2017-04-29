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
            {{--Up Vote--}}
            <a href="{{ $comment_unlike_url }}/{{$comment[\App\Utilities\Constants::FLD_COMMENTS_ID]}}"
               id="comment-{{ $comment[\App\Utilities\Constants::FLD_COMMENTS_ID] }}-down-vote-icon">
                @if(!isset($comment["user_vote"]) or $comment["user_vote"] != 0)
                    <i class="fa fa-thumbs-o-down" aria-hidden="true"> </i>
                @else
                    <i class="fa fa-thumbs-down" aria-hidden="true"></i>
                @endif
            </a>
            <span id="comment-{{ $comment[\App\Utilities\Constants::FLD_COMMENTS_ID] }}-down-votes-count">
                {{$comment[\App\Utilities\Constants::FLD_COMMENTS_DOWN_VOTES]}}
            </span>
            &nbsp;
            {{--Down Vote--}}
        <a href="{{$comment_like_url}}/{{$comment[\App\Utilities\Constants::FLD_COMMENTS_ID]}}"
           id="comment-{{ $comment[\App\Utilities\Constants::FLD_COMMENTS_ID] }}-up-vote-icon">
            @if(!isset($comment["user_vote"]) or $comment["user_vote"] != 1)
                <i class="fa fa-thumbs-o-up"aria-hidden="true"> </i>
            @else
                <i class="fa fa-thumbs-up"aria-hidden="true"> </i>
            @endif
        </a>
            <span id="comment-{{ $comment[\App\Utilities\Constants::FLD_COMMENTS_ID] }}-up-votes-count">
                {{$comment[\App\Utilities\Constants::FLD_COMMENTS_UP_VOTES]}}
            </span>
            &nbsp;
        </span>
        {{--Edit and delete if Applicable--}}
        <span>
            @if($comment['isOwner'])
                &nbsp; <i class="fa fa-pencil" aria-hidden="true" onclick="alert('edit pressed');"> edit</i>
                {{--<form action="/blogs/entry/{{$post[\App\Utilities\Constants::FLD_POSTS_ID]}}" method="POST" >--}}
                    {{--{{ csrf_field() }}--}}
                    {{--{{ method_field( 'delete' ) }}--}}
                    {{--<button type="submit" class="btn fa-input">--}}
                         {{--<i class="fa fa-trash" aria-hidden="true" > </i> delete--}}
                    {{--</button>--}}
                {{--</form>--}}
                &nbsp;  <i class="fa fa-trash" aria-hidden="true" onclick="app.deleteSinglePostComment('{{$comment[\App\Utilities\Constants::FLD_COMMENTS_ID]}}','/blogs/delete/comment/{{$post[\App\Utilities\Constants::FLD_POSTS_ID]}}', '{{csrf_token()}}')"> delete </i>
            @endif
        </span>
        {{--Reply Button --}}
        <span>
            &nbsp; <i class="fa fa-reply" aria-hidden="true" id="add-reply" > reply</i>
        </span>
        {{--Add Reply Form--}}
        @include('blogs.blogs_views.add_comment_form', [ 'expandableCommentForm' => 'true', 'parentID' ])
        {{--Show Comment Replies--}}
        @if( isset($comment[\App\Utilities\Constants::COMMENTS_REPLIES]))
            @foreach($comment[\App\Utilities\Constants::COMMENTS_REPLIES] as $comment)
                @include("blogs.blogs_views.comment")
            @endforeach
        @endif
    </div>
</div>

