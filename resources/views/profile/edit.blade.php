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

			<form method="post"  class="form-horizontal" role="form">
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
						<input name="LastName"class="form-control" value="user->name" type="text">
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-3 control-label">Country:</label>
					<div class="col-lg-8">
						<input name="Country" class="form-control" value="" type="text">
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-3 control-label">Email:</label>
					<div class="col-lg-8">
						<input name="Email" class="form-control" value="{{$user->email}}" type="text">
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-3 control-label">Time Zone:</label>
					<div class="col-lg-8">
						<div class="ui-select">
							<select id="user_time_zone" class="form-control">
								<option value="Hawaii">(GMT-10:00) Hawaii</option>
								<option value="Alaska">(GMT-09:00) Alaska</option>
								<option value="Pacific Time (US &amp; Canada)">(GMT-08:00) Pacific Time (US &amp; Canada)</option>
								<option value="Arizona">(GMT-07:00) Arizona</option>
								<option value="Mountain Time (US &amp; Canada)">(GMT-07:00) Mountain Time (US &amp; Canada)</option>
								<option value="Central Time (US &amp; Canada)" selected="selected">(GMT-06:00) Central Time (US &amp; Canada)</option>
								<option value="Eastern Time (US &amp; Canada)">(GMT-05:00) Eastern Time (US &amp; Canada)</option>
								<option value="Indiana (East)">(GMT-05:00) Indiana (East)</option>
							</select>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label">Username:</label>
					<div class="col-md-8">
						<input class="form-control" value="janeuser" type="text">
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label">Password:</label>
					<div class="col-md-8">
						<input class="form-control" value="11111122333" type="password">
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label">Confirm password:</label>
					<div class="col-md-8">
						<input class="form-control" value="11111122333" type="password">
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