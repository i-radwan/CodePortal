<div class="row">

    {{--Title--}}
    <h1 class="col-md-12">
        <a href="{{ route(\App\Utilities\Constants::ROUTES_BLOGS_POST_DISPLAY, $postID) }}">{{ $postTitle }}</a>
    </h1>

    {{--Subinfo--}}
    <div class="col-md-12">

        {{--Author--}}
        <p class="col-md-9">
            by
            <a href="{{ route(\App\Utilities\Constants::ROUTES_PROFILE, $postOwnerUsername) }}">{{ $postOwnerUsername }}</a>
        </p>

        {{--Actions--}}
        <span class="pull-right col-md-3">
        @if($isOwner)

                {{--Edit--}}
                <a href="{{ route(\App\Utilities\Constants::ROUTES_BLOGS_POST_EDIT, $postID) }}"
                   class="btn btn-link text-dark pull-right margin-5px">edit</a>

                {{--Delete--}}
                @include('components.action_form', ['halfWidth' => true, 'url' => route(\App\Utilities\Constants::ROUTES_BLOGS_POST_DELETE, $postID), 'method' => 'DELETE', 'confirm' => true, 'confirmMsg' => "'Are you sure want to delete this post? This action cannot be undone!'", 'btnIDs' => '', 'btnClasses' => 'btn btn-link text-dark pull-right margin-5px', 'btnTxt' => 'delete'])
            @endif
        </span>
    </div>
</div>

<p class="post-created-at">
    {{--Posted time--}}
    <span class="glyphicon glyphicon-time"></span>
    Posted {{ $postCreatedAt }}

    {{--Voting section--}}
    @include('blogs.blogs_views.voting_section',
        ['upVoteFormURL' => route(\App\Utilities\Constants::ROUTES_BLOGS_UPVOTE, $postID),
         'upVoteBtnID' => "blog-up-vote-icon",
         'upVoteCountID' => "blog-up-votes-count",
         'upVotesCount' => $upVotesCount,
         'didUserVote' => $didUserVote,
         'downVoteFormURL' => route(\App\Utilities\Constants::ROUTES_BLOGS_DOWNVOTE, $postID),
         'downVoteBtnID' => "blog-down-vote-icon",
         'downVoteCountID' => "blog-down-votes-count",
         'downVotesCount' => $downVotesCount])
</p>