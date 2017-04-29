<!-- Title -->
<h1>
    <a href="/blogs/entry/{{$post[\App\Utilities\Constants::FLD_POSTS_ID]}}">{{$post[\App\Utilities\Constants::FLD_POSTS_TITLE]}}</a>
</h1>


<div class="row">

    <!-- Author -->
    <p class=" lead col-md-10">
        by <a href="/profile/{{$post["username"]}}">{{$post["username"]}}</a>

    </p>

    <p class="col-md-2">
        <!-- Edit and Delete Buttons if available -->
    @if( $post['isOwner'])
        <!-- Edit Button -->
            <a  href="/blogs/edit/post/{{$post[\App\Utilities\Constants::FLD_POSTS_ID]}}">
                <i class="fa fa-pencil-square-o col-md-1" aria-hidden="true" style="font-size: 4.0vmin"></i>
            </a>
        <!-- Delete Button -->
            @include('components.action_form', ['url' => url('blogs/delete/post/'.$post[\App\Utilities\Constants::FLD_POSTS_ID]), 'method' => 'DELETE', 'confirm' => true, 'confirmMsg' => "'Are you sure want to delete this post? This action cannot be undone!'", 'btnIDs' => '', 'btnClasses' => 'btn btn-danger col-md-1', 'btnTxt' => 'Delete'])
    @endif
    </p>

</div>

<!-- Date/Time // Votes // Share Button(ToDO @ Samir) -->
<p><span class="glyphicon glyphicon-time"></span>
    Posted {{\App\Utilities\Utilities::formatPastDateTime($post[\App\Utilities\Constants::FLD_POSTS_CREATED_AT])}}
    &nbsp; &nbsp;<span>
        <a href="{{$post_unlike_url}}/{{$post[\App\Utilities\Constants::FLD_POSTS_ID]}}" id="blog-down-vote-icon">
            @if(!isset($post["user_vote"]) or $post["user_vote"] != 0)
                <i class="fa fa-thumbs-o-down" aria-hidden="true"> </i>
            @else
                <i class="fa fa-thumbs-down" aria-hidden="true"></i>
            @endif
        </a>
        <span id="blog-down-votes-count">{{$post[\App\Utilities\Constants::FLD_POSTS_DOWN_VOTES]}}</span> &nbsp; &nbsp;
        <a href="{{$post_like_url}}/{{$post[\App\Utilities\Constants::FLD_POSTS_ID]}}" id="blog-up-vote-icon">
            @if(!isset($post["user_vote"]) or $post["user_vote"] != 1)
                <i class="fa fa-thumbs-o-up" aria-hidden="true"> </i>
            @else
                <i class="fa fa-thumbs-up"aria-hidden="true"> </i>
            @endif
        </a>
        <span id="blog-up-votes-count">{{$post[\App\Utilities\Constants::FLD_POSTS_UP_VOTES]}}</span>
    </span>
</p>
<hr>

{{--TODO: @Samir Support Image--}}

@if(isset($post[\App\Utilities\Constants::FLD_POSTS_IMAGE]))
    <!-- Preview Image -->
    <img class="img-responsive" src="http://placehold.it/900x300" alt="">
    <hr>
@endif
