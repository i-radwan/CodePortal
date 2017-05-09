{{--Voting section--}}
@include('blogs.blogs_views.voting_section',
    ['upVoteFormURL' => route(\App\Utilities\Constants::ROUTES_BLOGS_COMMENT_UPVOTE, $commentID),
     'upVoteBtnID' => "comment-$commentID-up-vote-icon",
     'upVoteCountID' => "comment-$commentID-up-votes-count",
     'upVotesCount' => $upCommentVotesCount,
     'didUserVote' => $didUserVoteComment,
     'downVoteFormURL' => route(\App\Utilities\Constants::ROUTES_BLOGS_COMMENT_DOWNVOTE, $commentID),
     'downVoteBtnID' => "comment-$commentID-down-vote-icon",
     'downVoteCountID' => "comment-$commentID-down-votes-count",
     'downVotesCount' => $downCommentVotesCount])

{{--Edit and delete comment if Applicable--}}
<span class="comment-actions">
    @if($isOwner)
        <i class="fa fa-pencil edit-comment-icon" aria-hidden="true"
           onclick="app.editCommentClick(this);"> edit</i>

        <i class="fa fa-trash" aria-hidden="true"
           onclick="app.deleteSinglePostComment(this, '{{ route(\App\Utilities\Constants::ROUTES_BLOGS_COMMENT_DELETE, $commentID) }}', '{{ csrf_token() }}')"> delete</i>
    @endif
</span>

{{--Reply Button --}}
<span class="comment-actions">
    <i class="fa fa-reply reply-comment-icon" aria-hidden="true"
       onclick="app.showAddCommentSection(this);"> reply</i>
</span>

{{--Cancel Button --}}
<span class="comment-actions">
    <i class="fa fa-reply cancel-edit-comment-icon non-displayed-elements" aria-hidden="true"
       onclick="app.cancelEditComment(this)"> cancel</i>
</span>

{{--Save Button --}}
<span class="comment-actions">
    <i class="fa fa-save save-comment-icon non-displayed-elements" aria-hidden="true"
       onclick="app.updateComment(this, '{{ route(\App\Utilities\Constants::ROUTES_BLOGS_COMMENT_UPDATE, $commentID) }}','{{ csrf_token() }}')"> save</i>
</span>
