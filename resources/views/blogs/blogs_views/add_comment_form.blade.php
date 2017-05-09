{{--Add Comment Form--}}
<div class="well add-comment-section {{ ($displayable) ? '': 'non-displayed-elements' }}">

    <h4>Leave a Comment:</h4>

    {{--Comment Form--}}
    <form role="form" action="{{ $commentFormURL }}" method="POST">

        {{--Hidden Fields--}}
        {{csrf_field()}}

        {{--Parent Comment ID--}}
        <input name="{{\App\Utilities\Constants::FLD_COMMENTS_PARENT_ID}}"
               value="{{ (isset($comment[\App\Utilities\Constants::FLD_COMMENTS_ID]) ? $comment[\App\Utilities\Constants::FLD_COMMENTS_ID] : "") }}"
               type="hidden">

        {{--Post ID--}}
        <input name="{{\App\Utilities\Constants::FLD_COMMENTS_POST_ID}}"
               value="{{ $post[\App\Utilities\Constants::FLD_POSTS_ID] }}" type="hidden">

        {{--Form Fields--}}
        <div class="form-group">
            <textarea name="{{ \App\Utilities\Constants::FLD_COMMENTS_BODY }}"
                      class="form-control add-comment-text"></textarea>
        </div>

        {{--Submit--}}
        <button type="submit" class="btn btn-primary">Replay</button>
    </form>
</div>