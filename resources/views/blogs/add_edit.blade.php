@extends("layouts.app")
@section('content')
            <div class="container">
                <div class="panel panel-default">
                    <div class="panel-body">
                        {{--Alerts--}}
                        @include('components.alert')

                        <form class="form-horizontal" role="form" method="{{isset($postID) ? "put":"post" }}" action="/blogs/add">
                            {{csrf_field()}}

                            {{--Title--}}
                            <div class="form-group">
                                <label for="title" class="col-sm-2 control-label new_post_label">Title</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="post-title" name="title" placeholder="Title" value="{{isset($postTitle) ? $postTitle: ""}}">
                                </div>
                            </div>

                            {{--Body--}}
                            <div class="form-group">
                                <div>
                                    <label for="body" class="col-sm-2 control-label">Body</label>
                                </div>
                                <div class="col-md-10">
                                <textarea id="edit-post-body" name="body" class="form-control new-post-text-area" rows="15" placeholder="Write your Post Here using Markdown" contenteditable="true">
                                    {{isset($postBody) ? $postBody: ""}}
                                </textarea>
                                </div>
                            </div>

                            {{--Human Check--}}
                            {{--ToDo: @Samir Add CAPTCHA--}}
                            <div class="form-group">
                            </div>

                            {{--In editing: Post id Edit--}}
                            @if(isset($postID))
                                <input name="post_id" value="{{$postID}}" hidden>
                            @endif
                            {{--Submit Button--}}
                            <button type="submit" class="btn btn-primary center-block">{{isset($postID) ? "Save" :"Submit"}}</button>

                        </form>
                    </div>
                </div>
            </div>
            {{--Identifying Page --}}
            <span class="page-distinguishing-element" id="add-edit-post-page-hidden-element"></span>
@endsection
