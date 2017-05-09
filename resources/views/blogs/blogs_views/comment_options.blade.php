{{--Vote--}}
<span>
    {{--Up Vote--}}
    <a href="{{ $commentDownVoteURL }}/{{ $commentID }}" id="comment-{{ $commentID }}-down-vote-icon">

        {{--Up vote icon (highlighted if user already upvoted)--}}
        @if(!isset($didUserVoteComment) || $didUserVoteComment != \App\Utilities\Constants::RESOURCE_VOTE_TYPE_DOWN)
            <i class="fa fa-thumbs-o-down" aria-hidden="true"></i>
        @else
            <i class="fa fa-thumbs-down" aria-hidden="true"></i>
        @endif
    </a>

    {{--Comment upvotes count--}}
    <span id="comment-{{ $commentID }}-down-votes-count">
        {{ $downCommentVotesCount }}
    </span>

    {{--Down Vote--}}
    <a href="{{$commentUpVoteURL}}/{{ $commentID }}" id="comment-{{ $commentID }}-up-vote-icon">

        {{--Down vote icon (highlighted if user already downvoted)--}}
        @if(!isset($didUserVoteComment) || $didUserVoteComment != \App\Utilities\Constants::RESOURCE_VOTE_TYPE_UP)
            <i class="fa fa-thumbs-o-up" aria-hidden="true"></i>
        @else
            <i class="fa fa-thumbs-up" aria-hidden="true"></i>
        @endif
    </a>

    {{--Comment downvotes count--}}
    <span id="comment-{{ $commentID }}-up-votes-count">
        {{ $upCommentVotesCount }}
    </span>
       
</span>

{{--Edit and delete comment if Applicable--}}
<span class="comment-actions">
    @if($isOwner)
        <i class="fa fa-pencil edit-comment-icon" aria-hidden="true"
           onclick="app.editCommentClick(this);"> edit</i>

        <i class="fa fa-trash" aria-hidden="true"
           onclick="app.deleteSinglePostComment(this, '{{ $commentID }}', '/blogs/delete/comment/{{ $postID }}', '{{ csrf_token() }}')"> delete</i>
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
       onclick="app.updateComment(this, '{{ $commentID }}', '/blogs/edit/{{ $commentID }}','{{ csrf_token() }}')"> save</i>
</span>
