	<h3>Personal info</h3> <br>


		{{ csrf_field() }}

		<!-- first name -->
		<div class="form-group">
			<label class="col-lg-3 control-label">First name:</label>
			<div class="col-lg-8">
				<input name="FirstName" class="form-control" value="{{isset($user[Constants::FLD_USERS_FIRST_NAME]) ? $user[Constants::FLD_USERS_FIRST_NAME]: "" }}" type="text">
			</div>
		</div>

		<!-- last name -->
		<div class="form-group">
			<label class="col-lg-3 control-label">Last name:</label>
			<div class="col-lg-8">
				<input name="LastName"class="form-control" value="{{isset($user[Constants::FLD_USERS_LAST_NAME]) ? $user[Constants::FLD_USERS_LAST_NAME]: "" }}" type="text">
				<input type="hidden" name="id" value="{{$user->id}}">
			</div>
		</div>

		<!-- COUNTRY -->
		<div class="form-group">
			<label class="col-lg-3 control-label">Country:</label>
			<div class="col-lg-8">
				<select name="country" class="form-control" id="sel1" value="{{isset($user[Constants::FLD_USERS_COUNTRY]) ? $user[Constants::FLD_USERS_COUNTRY]: "" }}">
					@foreach($country as $key => $value)
					<option>{{ $value }}</option>
					@endforeach
					  @if($userCountry != null)
					  @endif
				</select>
			</div>
		</div>

		<!-- BIRTHDATE -->
		<div class="form-group">
			<label class="col-lg-3 control-label">Birth Date:</label>
			<div class="col-lg-8">
				<input style="background-color:white;"class="form-control" name="birthdate" value="{{isset($user[Constants::FLD_USERS_BIRTHDATE]) ? $user[Constants::FLD_USERS_BIRTHDATE]: "" }}"  type="date" id="datepicker" readonly> </div>
			</div>

		<!-- EMAIL -->
			<div class="form-group">
				<label class="col-lg-3 control-label">Email:</label>
				<div class="col-lg-8">
					<input name="email" class="form-control" value="{{$user[Constants::FLD_USERS_EMAIL]}}" type="text">
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
				 <h5> <kbd> <strong> {{$user[Constants::FLD_USERS_USERNAME]}} </strong> </kbd></h5>
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

		<!-- buttons -->
			<div class="form-group">
				<label class="col-md-3 control-label"></label>
				<div class="col-md-8">
					<input class="btn btn-primary" value="Save Changes" type="submit">
					<span></span>
					<a href="{{ route(Constants::ROUTES_PROFILE, $user[Constants::FLD_USERS_USERNAME]) }}">
					<input class="btn btn-default" value="Cancel" type="button">
					</a>
				</div>
			</div>