@php
    use App\Utilities\Constants;
    use \App\Utilities\Utilities;

    $postID = $post[Constants::FLD_POSTS_ID];
    $postTitle = $post[Constants::FLD_POSTS_TITLE];
    $postBody = $post[Constants::FLD_POSTS_BODY];
    $postCreatedAt = Utilities::formatPastDateTime($post[Constants::FLD_POSTS_CREATED_AT]);
    $postOwnerUsername = $post->owner[Constants::FLD_USERS_USERNAME];
    $isOwner = ((Auth::check()) ? (Auth::user()->posts()->find($postID) != null) : false);

    $didUserVote = ($post->didUserUpVote(Auth::user())) ? 1 : ($post->didUserDownVote(Auth::user()) ? 0 : -1);

    $downVotesCount = $post->downVotes()->count();
    $upVotesCount = $post->upVotes()->count();
@endphp

@extends('layouts.app')
@section('content')

    <div class="container">
        <div class="row">

            {{--Alerts--}}
            @include('components.alert')

            {{--Post Content--}}
            <div class="col-lg-8 blog-post-container">

                {{--Post Meta--}}
                @include('blogs.blogs_views.post_meta_info')

                {{--Post Body--}}
                <p class="post-paragraph"
                   id="current_post_body">{{ $postBody }}</p>
                <hr/>

                {{--Comment Section--}}
                @include("blogs.blogs_views.add_comment_form", ['displayable' => true])
                <hr/>

                {{--Comments--}}
                @foreach($comments->get() as $comment)
                    @include('blogs.blogs_views.comment')
                @endforeach
            </div>

            {{--Filters--}}
            @include('blogs.blogs_views.post_sidebar')

        </div>
    </div>

    {{--Identifying Page --}}
    <span class="page-distinguishing-element" id="view-post-page-hidden-element"></span>
@endsection
