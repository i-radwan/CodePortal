<!-- Blog Sidebar Widgets Column -->
<div class="col-md-4">
    <!-- New Post Button -->
    <div class="well-lg">
        <a class="new_post_link" href="/blogs/add"> <button class="btn btn-primary  center-block ">  New Post  </button></a>
    </div>
    <!-- Blog Search Well -->
    <div class="well">
        <form action="/blogs" method="get" role="form">
        <h4>Blog Search</h4>
        <div class="input-group">
            <input name="q" type="text" class="form-control" value="{{isset($q) ? $q: ""}}">
            <span class="input-group-btn">
                <button class="btn btn-default" type="submit">
                    <span class="glyphicon glyphicon-search"></span>
                </button>
            </span>
        </div>
        </form>
        <!-- /.input-group -->
    </div>

    {{--ToDo @ Samir To be Changed into Tags--}}
    {{--<!-- Blog Categories Well -->--}}
    {{--<div class="well">--}}
        {{--<h4>Blog Categories</h4>--}}
        {{--<div class="row">--}}
            {{--<div class="col-lg-6">--}}
                {{--<ul class="list-unstyled">--}}
                    {{--<li><a href="#">Category Name</a>--}}
                    {{--</li>--}}
                    {{--<li><a href="#">Category Name</a>--}}
                    {{--</li>--}}
                    {{--<li><a href="#">Category Name</a>--}}
                    {{--</li>--}}
                    {{--<li><a href="#">Category Name</a>--}}
                    {{--</li>--}}
                {{--</ul>--}}
            {{--</div>--}}
            {{--<!-- /.col-lg-6 -->--}}
            {{--<div class="col-lg-6">--}}
                {{--<ul class="list-unstyled">--}}
                    {{--<li><a href="#">Category Name</a>--}}
                    {{--</li>--}}
                    {{--<li><a href="#">Category Name</a>--}}
                    {{--</li>--}}
                    {{--<li><a href="#">Category Name</a>--}}
                    {{--</li>--}}
                    {{--<li><a href="#">Category Name</a>--}}
                    {{--</li>--}}
                {{--</ul>--}}
            {{--</div>--}}
            {{--<!-- /.col-lg-6 -->--}}
        {{--</div>--}}
        {{--<!-- /.row -->--}}
    {{--</div>--}}

    {{--ToDO: @Samir Add Top Contributors--}}
    <!-- Side Widget Well -->
    @if(isset($topContributors))
    <div class="well">
        <h4 style="color: blue;">Top Contributors</h4>
        <table class = 'table lead'>
            <tbody>
            @foreach( $topContributors as $userName => $contributions)
                <tr>
                    <td> {{$userName}}</td>
                    <td style="color: blue;">{{$contributions}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
