{{--Title--}}
<h1>
    <a href="/blogs/entry/{{ $postID }}">{{ $postTitle }}</a>
</h1>

<div class="row">

    {{--Author--}}
    <p class=" lead col-md-10">
        by <a href="/profile/{{ $postOwnerUsername }}">{{ $postOwnerUsername }}</a>

    </p>

    <p class="col-md-2">
        <!-- Edit and Delete Buttons if available -->
    @if($isOwner)
        <!-- Edit Button -->
            <a href="/blogs/edit/post/{{ $postID }}">
                <i class="fa fa-pencil-square-o col-md-1" aria-hidden="true" style="font-size: 4.0vmin;"></i>
            </a>
            <!-- Delete Button -->
            @include('components.action_form', ['url' => url('blogs/delete/post/'. $postID ), 'method' => 'DELETE', 'confirm' => true, 'confirmMsg' => "'Are you sure want to delete this post? This action cannot be undone!'", 'btnIDs' => '', 'btnClasses' => 'btn btn-danger col-md-1', 'btnTxt' => 'Delete'])
        @endif
    </p>

</div>

<!-- Date/Time // Votes // Share Button(ToDO @ Samir) -->
<p><span class="glyphicon glyphicon-time"></span>
    Posted {{\App\Utilities\Utilities::formatPastDateTime($postCreatedAt)}}
    &nbsp; &nbsp;<span>
        <a href="{{ $postDownVoteURL }}/{{ $postID }}" id="blog-down-vote-icon">
            @if(!isset($didUserVote) || $didUserVote != \App\Utilities\Constants::RESOURCE_VOTE_TYPE_DOWN)
                <i class="fa fa-thumbs-o-down" aria-hidden="true"></i>
            @else
                <i class="fa fa-thumbs-down" aria-hidden="true"></i>
            @endif
        </a>
        <span id="blog-down-votes-count">{{ $downVotesCount }}</span> &nbsp; &nbsp;
        <a href="{{ $postUpVoteURL }}/{{ $postID }}" id="blog-up-vote-icon">
            @if(!isset($didUserVote) || $didUserVote != \App\Utilities\Constants::RESOURCE_VOTE_TYPE_UP)
                <i class="fa fa-thumbs-o-up" aria-hidden="true"> </i>
            @else
                <i class="fa fa-thumbs-up" aria-hidden="true"> </i>
            @endif
        </a>
        <span id="blog-up-votes-count">{{ $upVotesCount }}</span>
    </span>
</p>