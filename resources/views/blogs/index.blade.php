@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            {{--Alerts Area--}}
            @include('components.alert')

            {{--Blog Entries Column--}}
            <div class="col-md-8">
                <h1 class="page-header">
                    Blogs
                </h1>

                @if(count($posts) > 0)

                    {{--Render Recent Posts--}}
                    @foreach( $posts as $post)

                        {{--Define variables--}}
                        @php
                            $postBody = $post[\App\Utilities\Constants::FLD_POSTS_BODY];
                        @endphp

                        {{--Post view--}}
                        @include("blogs.blogs_views.post_meta_info", ['post' => $post])
                        <p class="post_small_paragraph">{{ $postBody }}</p>
                        <hr>

                    @endforeach

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
            @include("blogs.blogs_views.side_filters")

        </div>
    </div>

    {{--Identifying Page --}}
    <span class="page-distinguishing-element" id="blogs-home-page-hidden-element"></span>
@endsection
