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
                                Recent Posts
                                {{--<small>Secondary Text</small>--}}
                                {{--TODO: @Samir--}}
                            </h1>

                            {{--Render Recent Posts--}}
                            @foreach( $posts as $post)
                                @include("blogs.blogs_views.post_brief_view")
                                <hr>
                            @endforeach


                            {{--TODO:Change Pagination style--}}
                            <ul class="pager">
                                <li class="previous">
                                    <a href="#">&larr; Older</a>
                                </li>
                                <li class="next">
                                    <a href="#">Newer &rarr;</a>
                                </li>
                            </ul>

                        </div>

                        {{--Side Panes--}}
                        @include("blogs.blogs_views.filters")

                    </div>

                </div>
        </div>
    </div>
@endsection
