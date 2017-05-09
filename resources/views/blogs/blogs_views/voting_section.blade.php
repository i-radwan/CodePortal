<div class="voting-section">
    {{--Up Vote--}}
    <form action="{{ $upVoteFormURL }}" class="form-inline" method="POST">

        {{--Hidden fields--}}
        {{ method_field('PUT') }}
        {{ csrf_field() }}

        <span onclick="$(this).parent().submit();" id="{{ $upVoteBtnID }}">
            {{--Up vote icon (highlighted if user already upvoted)--}}
            @if(!isset($didUserVote) || $didUserVote != \App\Utilities\Constants::RESOURCE_VOTE_TYPE_UP)
                <i class="fa fa-thumbs-o-up" aria-hidden="true"></i>
            @else
                <i class="fa fa-thumbs-up" aria-hidden="true"></i>
            @endif
        </span>
    </form>

    {{--Upvotes count--}}
    <span id="{{ $upVoteCountID }}">
        {{ $upVotesCount }}
    </span>

    {{--Down Vote--}}
    <form action="{{ $downVoteFormURL }}" class="form-inline" method="POST">

        {{--Hidden fields--}}
        {{ method_field('PUT') }}
        {{ csrf_field() }}

        <span onclick="$(this).parent().submit();" id="{{ $downVoteBtnID }}">
            {{--Down vote icon (highlighted if user already downvoted)--}}
            @if(!isset($didUserVote) || $didUserVote != \App\Utilities\Constants::RESOURCE_VOTE_TYPE_DOWN)
                <i class="fa fa-thumbs-o-down" aria-hidden="true"></i>
            @else
                <i class="fa fa-thumbs-down" aria-hidden="true"></i>
            @endif
        </span>
    </form>

    {{--Downvotes count--}}
    <span id="{{ $downVoteCountID }}">
        {{ $downVotesCount }}
    </span>
</div>