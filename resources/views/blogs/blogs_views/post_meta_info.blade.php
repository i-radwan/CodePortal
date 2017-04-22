<!-- Title -->
<h1>{{$post[\App\Utilities\Constants::FLD_POSTS_TITLE]}}</h1>

<!-- Author -->
<p class="lead">
by <a href="/profile/{{$post["username"]}}">{{$post["username"]}}</a>
</p>


<!-- Date/Time // Votes // Share Button(ToDO @ Samir) -->
<p><span class="glyphicon glyphicon-time"></span> Posted {{\App\Utilities\Utilities::formatPastDateTime($post[\App\Utilities\Constants::FLD_POSTS_CREATED_AT])}}
&nbsp; &nbsp;<span>  <i class="fa fa-thumbs-o-down" aria-hidden="true"></i></span> {{$post[\App\Utilities\Constants::FLD_POSTS_DOWN_VOTES]}} &nbsp; &nbsp; <i class="fa fa-thumbs-o-up" aria-hidden="true"></i> {{$post[\App\Utilities\Constants::FLD_POSTS_UP_VOTES]}}
</p>
<hr>

{{--TODO: @Samir Support Image--}}

@if(!isset($post[\App\Utilities\Constants::FLD_POSTS_IMAGE]))
<!-- Preview Image -->
<img class="img-responsive" src="http://placehold.it/900x300" alt="">
<hr>
@endif
