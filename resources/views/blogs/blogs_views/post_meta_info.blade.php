<div class="row">

    {{--Title--}}
    <h1 class="col-md-12">
        <a href="/blogs/entry/{{ $postID }}">{{ $postTitle }}</a>
    </h1>

    {{--Subinfo--}}
    <div class="col-md-12">

        {{--Author--}}
        <p class="col-md-9">
            by <a href="/profile/{{ $postOwnerUsername }}">{{ $postOwnerUsername }}</a>
        </p>

        {{--Actions--}}
        <span class="pull-right col-md-3">
        @if($isOwner)

                {{--Edit--}}
                <a href="/blogs/edit/post/{{ $postID }}"
                   class="btn btn-link text-dark pull-right margin-5px">edit</a>

                {{--Delete--}}
                @include('components.action_form', ['halfWidth' => true, 'url' => url('blogs/delete/post/'. $postID ), 'method' => 'DELETE', 'confirm' => true, 'confirmMsg' => "'Are you sure want to delete this post? This action cannot be undone!'", 'btnIDs' => '', 'btnClasses' => 'btn btn-link text-dark pull-right margin-5px', 'btnTxt' => 'delete'])
        @endif
        </span>
    </div>
</div>

<p>
    {{--Posted time--}}
    <span class="glyphicon glyphicon-time"></span>
    Posted {{ $postCreatedAt }}

    {{--Voting buttons--}}
    <span>

        {{--Upvote Link--}}
        <a href="{{ $postDownVoteURL }}/{{ $postID }}" id="blog-down-vote-icon">
            @if(!isset($didUserVote) || $didUserVote != \App\Utilities\Constants::RESOURCE_VOTE_TYPE_DOWN)
                <i class="fa fa-thumbs-o-down" aria-hidden="true"></i>
            @else
                <i class="fa fa-thumbs-down" aria-hidden="true"></i>
            @endif
        </a>

        {{--Upvotes count--}}
        <span id="blog-down-votes-count">{{ $downVotesCount }}</span>

        {{--Downvote Link--}}
        <a href="{{ $postUpVoteURL }}/{{ $postID }}" id="blog-up-vote-icon">
            @if(!isset($didUserVote) || $didUserVote != \App\Utilities\Constants::RESOURCE_VOTE_TYPE_UP)
                <i class="fa fa-thumbs-o-up" aria-hidden="true"> </i>
            @else
                <i class="fa fa-thumbs-up" aria-hidden="true"> </i>
            @endif
        </a>

        {{--Downvotes Count--}}
        <span id="blog-up-votes-count">{{ $upVotesCount }}</span>
    </span>
</p>