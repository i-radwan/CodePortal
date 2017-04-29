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
                <p class="post_paragraph" id="current_post_body" >  {{$post[\App\Utilities\Constants::FLD_POSTS_BODY]}} </p>
                <hr>
            </div>
            <!-- Blog Sidebar Widgets Column -->
            @include('blogs.blogs_views.side_filters')
        </div>
            <div class="row">
                <!-- Blog Add Comments Form -->
                <div class="container col-md-8">
                    @include("blogs.blogs_views.add_comment_form", ['expandableCommentForm' => 'false'])
                </div>
                <hr>
            <!-- Comments -->
                <div class="container col-md-8">
                    @foreach($comments as $comment)
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
