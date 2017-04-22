@extends('layouts.app')
@section('content')

<div class="container">
	<h1>Edit Profile</h1>
	<hr>
	@if(count($errors)>0)
	<ul>
		@foreach($errors->all() as $error)
		<li class="alert alert-danger">{{$error}}</li>
		@endforeach
	</ul>
	@endif
	<form method="post"  class="form-horizontal" role="form" action="{{ url('edit') }}" enctype="multipart/form-data">
		<div class="row">
			<!-- left column -->
			<div class="col-md-3">
				<div class="text-center">

					<img <img src="{{ asset('images/' . $user->profile_picture)}}" class="avatar img-circle" onerror=this.src="/images/profile/UserDefault.png" width="200" height="200" alt="avatar">

					<label class="control-label"></label>
					<input type="file" class="file" name="profile_picture">

				</div>
			</div>

			<!-- edit form column -->
			<div class="col-md-9 personal-info">

				<h3>Personal info</h3>


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
						<input name="LastName"class="form-control" value="{{isset($user->last_name) ? $user->last_name: "" }}" type="text">
						<input type="hidden" name="id" value="{{$user->id}}">
					</div>
				</div>
				<!-- COUNTRY -->

				<div class="form-group">
					<label class="col-lg-3 control-label">Country:</label>
					<div class="col-lg-8">
						<select name="country" class="form-control" id="sel1" value="{{isset($user->country) ? $user->country: "" }}">
							@foreach($country as $key => $value)
							<option>{{ $value }}</option>
							@endforeach
						</select>
					</div>
				</div>

				<!-- BIRTHDATE -->
				<div class="form-group">
					<label class="col-lg-3 control-label">Birth Date:</label>
					<div class="col-lg-8">
						<input style="background-color:white;"class="form-control" name="birthdate" value="{{isset($user->birthdate) ? $user->birthdate: "" }}"  type="date" id="datepicker" readonly> </div>
					</div>

				<!-- EMAIL -->
					<div class="form-group">
						<label class="col-lg-3 control-label">Email:</label>
						<div class="col-lg-8">
							<input name="email" class="form-control" value="{{$user->email}}" type="text">
						</div>
					</div>

				<!-- USERNAME -->
					<div class="form-group{{ $errors->has(Constants::FLD_USERS_USERNAME) ? ' has-error' : '' }} has-feedback">
						<label class="col-md-3 control-label">Username:</label>
						<div class="col-md-8">
							<input name="username" class="form-control" value="{{$user->username}}" type="text">
						</div>
					</div>
				<!-- GENDER -->
					<label class="col-lg-3 control-label" for="gender">Gender:   &#160;</label>
					<div class="controls col-lg-8 ">
						<label class="radio inline" for="gender-0">
							<input id="gender-0" name="gender" value="Male" checked="checked" type="radio">Male
						</label>
						<label class="radio inline" for="gender-1">
							<input id="gender-1" name="gender" value="Female" type="radio">Female
						</label>
						<br>
					</div>
				<!-- NEW PASSWORD -->
					<div class="form-group">
						<label class="col-md-3 control-label">New Password:</label>
						<div class="col-md-8">
						<input type="text" class="hide-input">
							<input  name="password"  class="form-control" value="" type="password">
						</div>
					</div>
				<!-- CONFIRM PASSWORD -->
					<div class="form-group">
						<label class="col-md-3 control-label">Confirm New password:</label>
						<div class="col-md-8">
							<input name="ConfirmPassword"  class="form-control" value="" type="password">
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