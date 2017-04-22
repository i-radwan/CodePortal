@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <!-- Page Content -->
            <div class="container">
                <div class="row">
                    <!-- Blog Entries Column -->
                    <div class="col-md-8">
                        <h1 class="page-header">
                            Most Recent
                            <small>Contributions</small>
                        </h1>
                         
                        {{--Render Recent Posts--}}
                        @foreach( $posts as $post)
                            @include("blogs.blogs_views.post_meta_info")
                            <p class = "post_paragraph">{{$post[\App\Utilities\Constants::FLD_POSTS_BODY]}}</p>
                            <hr>
                        @endforeach

                        {{--TODO:Change Pagination style--}}
                        {{$posts->render()}}

                    </div>

                    {{--Side Panes--}}
                    @include("blogs.blogs_views.side_filters")

                </div>

            </div>
        </div>
    </div>
@endsection
