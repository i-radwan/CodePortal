<!-- Title -->
<h1> <a href="/blogs/entry/{{$post[\App\Utilities\Constants::FLD_POSTS_POST_ID]}}">{{$post[\App\Utilities\Constants::FLD_POSTS_TITLE]}}</a></h1>

<!-- Author -->
<p class="lead">
by <a href="/profile/{{$post["username"]}}">{{$post["username"]}}</a>
</p>


<!-- Date/Time // Votes // Share Button(ToDO @ Samir) -->
<p><span class="glyphicon glyphicon-time"></span> Posted {{\App\Utilities\Utilities::formatPastDateTime($post[\App\Utilities\Constants::FLD_POSTS_CREATED_AT])}}
&nbsp; &nbsp;<span>
        <a href="{{$post_unlike_url}}/{{$post[\App\Utilities\Constants::FLD_POSTS_POST_ID]}}">
            @if(!isset($post["user_vote"]) or $post["user_vote"] != 0)
            <i class="fa fa-thumbs-o-down" aria-hidden="true"> </i>
            @else
                <i class="fa fa-thumbs-down" aria-hidden="true"></i>
            @endif
        </a>
        {{$post[\App\Utilities\Constants::FLD_POSTS_DOWN_VOTES]}} &nbsp; &nbsp;
        <a href="{{$post_like_url}}/{{$post[\App\Utilities\Constants::FLD_POSTS_POST_ID]}}">
            @if(!isset($post["user_vote"]) or $post["user_vote"] != 1)
                <i class="fa fa-thumbs-o-up" aria-hidden="true"> </i>
            @else
                <i class="fa fa-thumbs-up" aria-hidden="true"> </i>
            @endif
        </a>
        {{$post[\App\Utilities\Constants::FLD_POSTS_UP_VOTES]}}
    </span>
</p>
<hr>

{{--TODO: @Samir Support Image--}}

@if(!isset($post[\App\Utilities\Constants::FLD_POSTS_IMAGE]))
<!-- Preview Image -->
<img class="img-responsive" src="http://placehold.it/900x300" alt="">
<hr>
@endif
