@php
    use App\Utilities\Constants;
    use App\Utilities\Utilities;

    $commentID = $comment[Constants::FLD_COMMENTS_ID];
    $commentBody = $comment[Constants::FLD_COMMENTS_BODY];
    $commentUsername = $comment->user[Constants::FLD_USERS_ID];
    $commentCreatedAt = Utilities::formatPastDateTime($comment[Constants::FLD_COMMENTS_CREATED_AT]);

    $didUserVoteComment = ($comment->upVotes()->ofUser(Auth::user()[Constants::FLD_USERS_ID])->count()) ? 1 :
    ($comment->downVotes()->ofUser(Auth::user()[Constants::FLD_USERS_ID])->count() ? 0 : -1);

    $downCommentVotesCount = $comment->downVotes()->count();
    $upCommentVotesCount = $comment->upVotes()->count();

@endphp

<!-- Comment -->
<div class="media">

    {{--To BE Later Use the User profile Photo--}}
    <a class="pull-left" href="#">
        <img class="media-object" src="https://placeholdit.imgix.net/~text?txtsize=22&txt=Hi&w=60&h=60" alt="">
    </a>
    <div class="media-body">

        <h4 class="media-heading">
            {{--user Name--}}
            <a href='/profile/{{ $commentUsername }}'>{{ $commentUsername }}</a>

            {{--date and time--}}
            <small>{{ $commentCreatedAt }}</small>
        </h4>

        {{--body--}}
        <div class="well">
            <p class="comment-body">{{ $commentBody }}</p>
        </div>

        {{--Add Comment Options--}}
        @include('blogs.blogs_views.comment_options')

        {{--Add Reply Form--}}
        @include('blogs.blogs_views.add_comment_form', ['displayable' => false])

        {{--Show Comment Replies--}}
        @if( isset($comment->replies))
            @foreach($comment->replies as $comment)
                @include("blogs.blogs_views.comment")
            @endforeach

        @endif
    </div>
</div>

