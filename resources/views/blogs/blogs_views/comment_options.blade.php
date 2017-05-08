{{--Vote--}}
<span>
        {{--Up Vote--}}
    <a href="{{ $commentDownVoteURL }}/{{ $commentID }}"
       id="comment-{{ $commentID }}-down-vote-icon">
        @if(!isset($didUserVoteComment) || $didUserVoteComment != \App\Utilities\Constants::RESOURCE_VOTE_TYPE_DOWN)
            <i class="fa fa-thumbs-o-down" aria-hidden="true"> </i>
        @else
            <i class="fa fa-thumbs-down" aria-hidden="true"></i>
        @endif
        </a>
        <span id="comment-{{ $commentID }}-down-votes-count">
            {{ $downCommentVotesCount }}
        </span>
        &nbsp;
    {{--Down Vote--}}
    <a href="{{$commentUpVoteURL}}/{{ $commentID }}"
       id="comment-{{ $commentID }}-up-vote-icon">
            @if(!isset($didUserVoteComment) || $didUserVoteComment != \App\Utilities\Constants::RESOURCE_VOTE_TYPE_UP)
            <i class="fa fa-thumbs-o-up" aria-hidden="true"> </i>
        @else
            <i class="fa fa-thumbs-up" aria-hidden="true"> </i>
        @endif
    </a>
        <span id="comment-{{ $commentID }}-up-votes-count">
            {{ $upCommentVotesCount }}
        </span>
        &nbsp;
    </span>
{{--Edit and delete if Applicable--}}
<span>
    @if($comment['isOwner'])
        &nbsp; <i class="fa fa-pencil edit-comment-icon" aria-hidden="true"
                  onclick="app.editCommentClick(this);"> edit</i>
        &nbsp;  <i class="fa fa-trash" aria-hidden="true"
                   onclick="app.deleteSinglePostComment(this, '{{ $commentID }}','/blogs/delete/comment/{{ $postID }}', '{{csrf_token()}}')"> delete </i>
    @endif
</span>
{{--Reply Button --}}
<span>
    &nbsp; <i class="fa fa-reply reply-comment-icon" aria-hidden="true" id="add-reply"
              onclick="app.showAddCommentSection(this);"> reply</i>
</span>
{{--cancel Button --}}
<span>
    &nbsp; <i class="fa fa-reply cancel-edit-comment-icon non-displayed-elements" aria-hidden="true"
              id="cancel-edit" onclick="app.cancelEditComment(this)"> cancel</i>
</span>
{{--save Button --}}
<span>
&nbsp; <i class="fa fa-save save-comment-icon non-displayed-elements" aria-hidden="true" id="save-edit"
          onclick="app.updateComment(this, '{{ $commentID }}', '/blogs/edit/{{ $commentID }}','{{csrf_token()}}')"> save</i>
</span>
