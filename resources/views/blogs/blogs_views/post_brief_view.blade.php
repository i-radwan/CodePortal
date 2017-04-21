<h2>
    <a href="#">{{$post[\App\Utilities\Constants::FLD_POSTS_TITLE]}}</a>
</h2>
<p class="lead">
    by <a href="/profile/{{$post[\App\Utilities\Constants::FLD_POSTS_POST_ID]}}">{{$post[\App\Utilities\Constants::FLD_POSTS_OWNER_ID]}}</a>
</p>
<p><span class="glyphicon glyphicon-time"></span> Posted {{\App\Utilities\Utilities::formatPastDateTime($post[\App\Utilities\Constants::FLD_POSTS_CREATED_AT])}}</p>
<hr>
@if( isset($post[\App\Utilities\Constants::FLD_POSTS_IMAGE]))
    <img class="img-responsive" src="http://placehold.it/900x300" alt="">
    <hr>
@endif
<p>{{$post[\App\Utilities\Constants::FLD_POSTS_BODY]}}</p>
<a class="btn btn-primary" href="#">Read More <span class="glyphicon glyphicon-chevron-right"></span></a>
