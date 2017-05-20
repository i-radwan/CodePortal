@php
    use App\Utilities\Constants;

    if(isset($post)) {
        $postID = $post[Constants::FLD_POSTS_ID];
        $postTitle = $post[Constants::FLD_POSTS_TITLE];
        $postBody = $post[Constants::FLD_POSTS_BODY];
    }
@endphp
@extends("layouts.app")
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">{{ isset($postTitle) ? "Edit " . $postTitle : 'Add Post' }}
            </div>

            <div class="panel-body">

                {{--Alerts--}}
                @include('components.alert')

                {{--New post form--}}
                <form class="form-horizontal" role="form" method="post" onsubmit="return app.submitAddOrEditPostForm(this)"
                      action= {{ isset($postID) ? route(\App\Utilities\Constants::ROUTES_BLOGS_POST_UPDATE, $postID) : route(\App\Utilities\Constants::ROUTES_BLOGS_POST_STORE) }}>

                    <span  name="edit-post-error" class="centered"> </span>

                    {{--Hidden fields--}}
                    @if(isset($postID))
                        {{method_field('PUT')}}
                    @endif
                    {{csrf_field()}}

                    {{--Post ID for editing--}}
                    @if(isset($postID))
                        <input name="post_id" value="{{$postID}}" type="hidden"/>
                    @endif

                    {{--Title--}}
                    <div class="form-group">
                        <label for="title" class="col-sm-2 control-label new-post-label">Title</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="post-title" name="title" placeholder="Title"
                                   value="{{ isset($postTitle) ? $postTitle: "" }}" autocomplete="off" minlength="6" required>
                        </div>
                    </div>

                    {{--Body--}}
                    <div class="form-group">
                        <div>
                            <label for="body" class="col-sm-2 control-label">Body</label>
                        </div>
                        <div class="col-md-10">
                                <textarea id="edit-post-body" name="body" class="form-control new-post-text-area"
                                          contenteditable="true"
                                          data-autosave-enable="{{ isset($postBody) ? "false" : "true" }}">
                                    {{ isset($postBody) ? $postBody: "" }}
                                </textarea>
                        </div>
                    </div>

                    {{--Submit Button--}}
                    <button type="submit"
                            class="btn btn-primary center-block">{{ isset($postID) ? "Save" :"Add" }}</button>
                </form>
            </div>
        </div>
    </div>

    {{--Identifying Page --}}
    <span class="page-distinguishing-element" id="add-edit-post-page-hidden-element"></span>
@endsection
