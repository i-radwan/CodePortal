<!-- Comments Form -->
<div class="well">
    <h4>Leave a Comment:</h4>
    <form role="form" action="{{$comment_form_url}}" method="POST">
        {{csrf_field()}}
        <div class="form-group">
            <textarea id="add-comment-text" name="body" class="form-control"  ></textarea>
        </div>
        <div class="form-group">
            <input name="comment_id" value="{{isset($comment[\App\Utilities\Constants::FLD_COMMENTS_COMMENT_ID]) ? $comment[\App\Utilities\Constants::FLD_COMMENTS_COMMENT_ID] : ""}}" hidden>
        </div>
        <div class="form-group">
            <input name="post_id" value="{{$post[\App\Utilities\Constants::FLD_POSTS_ID]}}" hidden>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<hr>
