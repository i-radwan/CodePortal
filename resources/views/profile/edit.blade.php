@extends('layouts.app')
@section('content')
<div class="container">
	<h1>Edit Profile</h1>
	<hr>
	<div class="row">
		<!-- left column -->
		<div class="col-md-3">
			<div class="text-center">
				<img src="/images/profile/UserSmall.png" class="avatar img-circle" alt="avatar">
				<label class="control-label"></label>
				<input type="file" class="file">
			</div>
		</div>

		<!-- edit form column -->
		<div class="col-md-9 personal-info">
			<div class="alert alert-info alert-dismissable">
				<a class="panel-close close" data-dismiss="alert">×</a> 
				<i class="fa fa-coffee"></i>
				This is an <strong>.alert</strong>. Use this to show important messages to the user.
			</div>
			<h3>Personal info</h3>




			@if(count($errors)>0)
			<ul>
			@foreach($errors->all() as $error)
			<li class="alert alert-danger">{{$error}}</li>
			@endforeach
			</ul>
			@endif
			<form method="post"  class="form-horizontal" role="form" action="{{ url('edit') }}">
			{{ csrf_field() }}
				<div class="form-group">
					<label class="col-lg-3 control-label">First name:</label>
					<div class="col-lg-8">
						<input name="FirstName" class="form-control" value="{{isset($user->first_name) ? $user->first_name: "" }}" type="text">
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-3 control-label">Last name:</label>
					<div class="col-lg-8">
						<input name="LastName"class="form-control" value="{{isset($user->last_name) ? $user->first_name: "" }}" type="text">
						<input type="hidden" name="id" value="{{$user->id}}">
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-3 control-label">Country:</label>
					<div class="col-lg-8">
						<input name="Country" class="form-control" value="{{$user->country}}" type="text">
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-3 control-label">Email:</label>
					<div class="col-lg-8">
						<input name="email" class="form-control" value="{{$user->email}}" type="text">
					</div>
				</div>
				<div class="form-group{{ $errors->has(Constants::FLD_USERS_USERNAME) ? ' has-error' : '' }} has-feedback">
					<label class="col-md-3 control-label">Username:</label>
					<div class="col-md-8">
						<input name="username" class="form-control" value="{{$user->username}}" type="text">
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label">New Password:</label>
					<div class="col-md-8">
						<input class="form-control" value="" type="password">
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label">Confirm password:</label>
					<div class="col-md-8">
						<input class="form-control" value="" type="password">
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label"></label>
					<div class="col-md-8">
						<input class="btn btn-primary" value="Save Changes" type="submit">
						<span></span>
						<input class="btn btn-default" value="Cancel" type="reset">
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<hr>
@endsection