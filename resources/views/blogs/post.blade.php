@php
    use App\Utilities\Constants;

    $postID = $post[Constants::FLD_POSTS_ID];
    $postTitle = $post[Constants::FLD_POSTS_TITLE];
    $postBody = $post[Constants::FLD_POSTS_BODY];
    $postCreatedAt = $post[Constants::FLD_POSTS_CREATED_AT];
    $postOwnerUsername = $post->owner[Constants::FLD_USERS_USERNAME];
    $isOwner = ((Auth::check()) ? (Auth::user()->posts()->find($postID) != null) : false);

    $didUserVote = ($post->upVotes()->ofUser(Auth::user()[Constants::FLD_USERS_ID])->count()) ? 1 :
    ($post->downVotes()->ofUser(Auth::user()[Constants::FLD_USERS_ID])->count() ? 0 : -1);

    $downVotesCount = $post->downVotes()->count();
    $upVotesCount = $post->upVotes()->count();
@endphp

@extends('layouts.app')
@section('content')
    <!-- Page Content -->
    <div class="container">
        <div class="row">
        {{--Alerts--}}
        @include('components.alert')
        <!-- Blog Post Content Column -->
            <div class="col-lg-8">
                <!-- Blog Post -->
            @include('blogs.blogs_views.post_meta_info')
            {{--Render Post--}}
            <!-- Post Content -->
                <p class="post_paragraph"
                   id="current_post_body">  {{$postBody }} </p>
                <hr>
            </div>
            <!-- Blog Sidebar Widgets Column -->
            @include('blogs.blogs_views.side_filters')
        </div>
        <div class="row">
            <!-- Blog Add Comments Form -->
            <div class="container col-md-8">
                @include("blogs.blogs_views.add_comment_form", ['expandableCommentForm' => 'false', 'displayable' => true])
            </div>
            <hr>
            <!-- Comments -->
            <div class="container col-md-8">
                @foreach($comments->get() as $comment)
                    @include('blogs.blogs_views.comment')
                @endforeach
            </div>

        </div>
        <!-- /.row -->
        <hr>
    </div>
    {{--Identifying Page --}}
    <span class="page-distinguishing-element" id="view-post-page-hidden-element"></span>
@endsection
