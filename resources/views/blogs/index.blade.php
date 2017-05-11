@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">

            {{--Blog Entries Column--}}
            <div class="col-md-8">

                {{--Heading--}}
                <h1 class="page-header">
                    Blogs
                </h1>

                {{--Alerts Area--}}
                @include('components.alert')

                @if(count($posts) > 0)

                    {{--Render Recent Posts--}}
                    @foreach($posts as $post)

                        {{--Define variables--}}
                        @php
                            $postID = $post[\App\Utilities\Constants::FLD_POSTS_ID];
                            $postBody = $post[\App\Utilities\Constants::FLD_POSTS_BODY];
                            $postTitle = $post[\App\Utilities\Constants::FLD_POSTS_TITLE];
                            $postOwnerUsername = $post->owner[\App\Utilities\Constants::FLD_USERS_USERNAME];
                            $isOwner = ((Auth::check()) ? (Auth::user()->posts()->find($postID) != null) : false);
                            $postCreatedAt = \App\Utilities\Utilities::formatPastDateTime($post[\App\Utilities\Constants::FLD_POSTS_CREATED_AT]);
                            $downVotesCount = $post->downVotes()->count();
                            $upVotesCount = $post->upVotes()->count();
                            $didUserVote = ((Auth::check()) ? (($post->didUserUpVote(Auth::user())) ? 1 : ($post->didUserDownVote(Auth::user()) ? 0 : -1)) : false);
                        @endphp

                        {{--Post meta info view--}}
                        @include("blogs.blogs_views.post_meta_info")

                        <p class="post-small-paragraph">{{ $postBody }}</p>

                        <hr/>

                    @endforeach

                    {{--Pagination--}}
                    {{ $posts->render() }}

                @else
                    @if($q != '')
                        <p class="lead">Sorry, no blogs were posted yet!</p>
                    @else
                        <p class="lead">Sorry, no blogs meeting your search word.</p>
                    @endif
                @endif
            </div>

            {{--Side Panes--}}
            @include("blogs.blogs_views.post_sidebar")

        </div>
    </div>

    {{--Identifying Page --}}
    <span class="page-distinguishing-element" id="blogs-home-page-hidden-element"></span>
@endsection
