@extends('layouts.app')
@section('content')

<div class="container">
	<h1>Edit Profile</h1>
	<hr>
	@include('components.alert')
	<form method="post"  class="form-horizontal" role="form" action="{{ route(\App\Utilities\Constants::ROUTES_PROFILE_UPDATE) }}" enctype="multipart/form-data">
		<div class="row">
			<!-- left column -->
			<div class="col-md-3">
				<div class="text-center">

					<img src="{{ asset('images/' . $user->profile_picture)}}" class="avatar img-circle" onerror=this.src="/images/profile/UserDefault.png"; width="200" height="200" alt="avatar">

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

					<!-- CodeForces Handle -->
					<div class="form-group">
						<label class="col-lg-3 control-label">Codeforces Handle:</label>
						<div class="col-lg-8">
							<input name="{{ \App\Utilities\Constants::FLD_USERS_CODEFORCES_HANDLE }}" class="form-control" value="{{isset($handle['0']['original']['pivot_handle']) ? $handle['0']['original']['pivot_handle']: "" }}" type="text">
						</div>
					</div>

					<!-- UVA Handle -->
					<div class="form-group">
						<label class="col-lg-3 control-label">Uva Handle:</label>
						<div class="col-lg-8">
							<input name="{{ \App\Utilities\Constants::FLD_USERS_UVA_HANDLE }}" class="form-control" value="{{isset($handle['1']['original']['pivot_handle']) ? $handle['1']['original']['pivot_handle']: "" }}" type="text">
						</div>
					</div>

					<!-- Live Archive Handle -->
					<div class="form-group">
						<label class="col-lg-3 control-label">Live Archive Handle:</label>
						<div class="col-lg-8">
							<input name="{{ \App\Utilities\Constants::FLD_USERS_LIVE_ARCHIVE_HANDLE }}" class="form-control" value="{{isset($handle['2']['original']['pivot_handle']) ? $handle['2']['original']['pivot_handle']: "" }}" type="text">
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

					<!-- USERNAME -->
					<div class="form-group">
						<label class="col-md-3 control-label">Username:</label>
						<div class="col-md-8">
						 <h5> <kbd> <strong> {{$user->username}} </strong> </kbd></h5>
						</div>
					</div>

					
				<!-- NEW PASSWORD -->
					<div class="form-group">
						<label class="col-md-3 control-label">New Password:</label>
						<div class="col-md-8">
						<input type="text" class="hide-input">
							<input  name="password"  class="form-control" value="" type="password">
						</div>
					</div>
				<!-- OLD PASSWORD -->
					<div class="form-group required">
						<label class="col-md-3 control-label">Old Password:</label>
						<div class="col-md-8">
							<input  name="oldPassword"  class="form-control" value="" type="password">
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"></label>
						<div class="col-md-8">
							<input class="btn btn-primary" value="Save Changes" type="submit">
							<span></span>
							{{--TODO: use route name--}}
							<a href="/profile/{{ $user->username }}">
							<input class="btn btn-default" value="Cancel" type="button">
							</a>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<hr>

	@endsection