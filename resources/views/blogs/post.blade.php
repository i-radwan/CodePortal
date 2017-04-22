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
            <p class="lead"> <p> {{$post[\App\Utilities\Constants::FLD_POSTS_BODY]}} </p>
            <hr>

            <!-- Blog Add Comments Form -->
            @include("blogs.blogs_views.add_comment_form")

            <!-- Comments -->
            @foreach($comments as $comment)
            @include('blogs.blogs_views.comment')
            @endforeach

        </div>

        <!-- Blog Sidebar Widgets Column -->
        @include('blogs.blogs_views.side_filters')

    </div>
    <!-- /.row -->
    <hr>
</div>
@endsection
