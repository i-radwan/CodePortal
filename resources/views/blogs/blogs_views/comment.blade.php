@php
    use App\Utilities\Constants;
    use App\Utilities\Utilities;

    $commentID = $comment[Constants::FLD_COMMENTS_ID];
    $commentBody = $comment[Constants::FLD_COMMENTS_BODY];
    $commentUsername = $comment->owner[Constants::FLD_USERS_USERNAME];
    $commentCreatedAt = Utilities::formatPastDateTime($comment[Constants::FLD_COMMENTS_CREATED_AT]);

    $didUserVoteComment = ($comment->didUserUpVote(Auth::user())) ? 1 : ($comment->didUserDownVote(Auth::user()) ? 0 : -1);

    $downCommentVotesCount = $comment->downVotes()->count();
    $upCommentVotesCount = $comment->upVotes()->count();

@endphp

<div class="media">

    {{--User profile photo--}}
    {{--ToDo: Use user profile photo @Samir --}}
    <a class="pull-left" href="#">
        <img class="media-object" src="https://placeholdit.imgix.net/~text?txtsize=22&txt=Hi&w=60&h=60" alt="">
    </a>

    <div class="media-body">

        <h4 class="media-heading">
            {{--Username--}}
            <a href='/profile/{{ $commentUsername }}'>{{ $commentUsername }}</a>

            {{--Date and Time--}}
            <small class="pull-right">{{ $commentCreatedAt }}</small>
        </h4>

        {{--Body--}}
        <div class="well">
            <p class="comment-body">{{ $commentBody }}</p>
        </div>

        {{--Comment Options--}}
        @include('blogs.blogs_views.comment_options')

        {{--Add Reply Form--}}
        @include('blogs.blogs_views.add_comment_form', ['displayable' => false])

        {{--Show Comment Replies--}}
        @foreach($comment->replies as $comment)
            @include("blogs.blogs_views.comment")
        @endforeach

    </div>
</div>

