<!-- Comments Form -->
<div class="well add-comment-section {{ ($displayable) ? '': 'non-displayed-elements' }}">
    <h4>Leave a Comment:</h4>
    <form role="form" action="{{ $commentFormURL }}" method="POST">
        {{csrf_field()}}
        <div class="form-group">
            <textarea name="{{\App\Utilities\Constants::FLD_COMMENTS_BODY}}"
                      class="form-control add-comment-text"></textarea>
        </div>
        <div class="form-group">
            <input name="{{\App\Utilities\Constants::FLD_COMMENTS_PARENT_ID}}"
                   value="{{isset($comment[\App\Utilities\Constants::FLD_COMMENTS_ID]) ? $comment[\App\Utilities\Constants::FLD_COMMENTS_ID] : ""}}"
                   hidden>
        </div>
        <div class="form-group">
            <input name="{{\App\Utilities\Constants::FLD_COMMENTS_POST_ID}}"
                   value="{{$post[\App\Utilities\Constants::FLD_POSTS_ID]}}" hidden>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<hr>
