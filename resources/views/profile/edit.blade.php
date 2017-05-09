@php
$userCountry = $user[Constants::FLD_USERS_COUNTRY];
$handle = $user->handles()->get();
@endphp

@extends('layouts.app')
@section('content')

<div class="container">
	<h1>Edit Profile</h1>
	<hr>
	@include('components.alert')

	<!-- edit form -->
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

			<!-- right column -->
			<div class="col-md-9 personal-info">
			@include('profile.edit_form_data')
			</div>

		</div>
	</form>
</div>
 <hr>

@endsection

