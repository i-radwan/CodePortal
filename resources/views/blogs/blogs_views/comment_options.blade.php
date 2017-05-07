

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
            <i class="fa fa-thumbs-o-up" aria-hidden="true"> </i>
        @else
            <i class="fa fa-thumbs-up" aria-hidden="true"> </i>
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
        &nbsp; <i class="fa fa-pencil edit-comment-icon" aria-hidden="true"
                  onclick="app.editCommentClick(this);"> edit</i>
        &nbsp;  <i class="fa fa-trash" aria-hidden="true"
                   onclick="app.deleteSinglePostComment(this, '{{$comment[\App\Utilities\Constants::FLD_COMMENTS_ID]}}','/blogs/delete/comment/{{$post[\App\Utilities\Constants::FLD_POSTS_ID]}}', '{{csrf_token()}}')"> delete </i>
    @endif
</span>
{{--Reply Button --}}
<span>
    &nbsp; <i class="fa fa-reply reply-comment-icon" aria-hidden="true" id="add-reply" onclick=""> reply</i>
</span>
{{--cancel Button --}}
<span>
    &nbsp; <i class="fa fa-reply cancel-edit-comment-icon non-displayed-elements" aria-hidden="true"
              id="cancel-edit" onclick="app.cancelEditComment(this)"> cancel</i>
</span>
{{--cancel Button --}}
<span>
&nbsp; <i class="fa fa-save save-comment-icon non-displayed-elements" aria-hidden="true" id="save-edit"
          onclick="app.updateComment(this, '{{ $comment['id'] }}', '/blogs/edit/{{$comment[\App\Utilities\Constants::FLD_COMMENTS_ID]}}}}','{{csrf_token()}}')"> save</i>
</span>
