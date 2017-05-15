{{--First name--}}
<div class="form-group">
    <label class="col-lg-3 control-label">First name:</label>
    <div class="col-lg-8">
        <input name="first_name" class="form-control"
               placeholder="First name..."
               value="{{ $firstName }}"
               type="text"/>
    </div>
</div>

{{--Last Name--}}
<div class="form-group">
    <label class="col-lg-3 control-label">Last name:</label>
    <div class="col-lg-8">
        <input name="last_name" class="form-control"
               placeholder="Last name..."
               value="{{ $lastName }}"
               type="text"/>
    </div>
</div>

{{--Email--}}
<div class="form-group">
    <label class="col-lg-3 control-label">Email:</label>
    <div class="col-lg-8">
        <input name="email" class="form-control"
               placeholder="Email..." value="{{ $email }}" type="text"/>
    </div>
</div>

{{--Username--}}
<div class="form-group">
    <label class="col-md-3 control-label">Username:</label>
    <div class="col-md-8">
        <p class="vertical-align padding-top-5px">
            <strong>{{ $username }}</strong>
        </p>
    </div>
</div>

{{--Country--}}
<div class="form-group">
    <label class="col-lg-3 control-label">Country:</label>
    <div class="col-lg-8">
        <select name="country" class="form-control" id="sel1"
                value="{{ $country }}">

            {{--Options--}}
            <option>Please select country...</option>
            @foreach($countries as $country)
                <option>{{ $country }}</option>
            @endforeach

        </select>
    </div>
</div>

{{--BirthDate--}}
<div class="form-group">
    <label class="col-lg-3 control-label">Birth Date:</label>
    <div class="col-lg-8">
        <input style="background-color:white;" class="form-control" name="birthdate"
               value="{{ $birthDate }}" placeholder="Birth date..."
               type="date" id="datepicker" readonly/>
    </div>
</div>

{{--Codeforces Handle--}}
<div class="form-group">
    <label class="col-lg-3 control-label">Codeforces Handle:</label>
    <div class="col-lg-8">
        <input name="{{ \App\Utilities\Constants::FLD_USERS_CODEFORCES_HANDLE }}" class="form-control"
               value="{{ $codeforcesHandle }}" placeholder="Handle..." type="text"/>
    </div>
</div>

{{--UVA Handle--}}
<div class="form-group">
    <label class="col-lg-3 control-label">Uva Handle:</label>
    <div class="col-lg-8">
        <input name="{{ \App\Utilities\Constants::FLD_USERS_UVA_HANDLE }}" class="form-control"
               value="{{ $uvaHandle }}" placeholder="Handle..." type="text"/>
    </div>
</div>

{{--Live Archive Handle--}}
<div class="form-group">
    <label class="col-lg-3 control-label">Live Archive Handle:</label>
    <div class="col-lg-8">
        <input name="{{ \App\Utilities\Constants::FLD_USERS_LIVE_ARCHIVE_HANDLE }}" class="form-control"
               value="{{ $liveArchiveHandle  }}" placeholder="Handle..." type="text"/>
    </div>
</div>

{{--Gender--}}
<label class="col-lg-3 control-label" for="gender">Gender: &#160;</label>
<div class="controls col-lg-8">
    <label class="radio inline margin-15px" for="gender-0">
        <input id="gender-0" name="gender" value="0" checked="checked" type="radio">Male
    </label>
    <label class="radio inline margin-15px" for="gender-1">
        <input id="gender-1" name="gender" value="1" type="radio">Female
    </label>
    <br>
</div>

{{--New password--}}
<div class="form-group">
    <label class="col-md-3 control-label">New Password:</label>
    <div class="col-md-8">
        <input name="password" placeholder="••••••••" class="form-control" value="" type="password">
    </div>
</div>

{{--Old password--}}
<div class="form-group required">
    <label class="col-md-3 control-label">Old Password:</label>
    <div class="col-md-8">
        <input name="oldPassword" placeholder="••••••••" class="form-control" value="" type="password" required>
    </div>
</div>

{{--Buttons--}}
<div class="form-group">
    <label class="col-md-3 control-label"></label>
    <div class="col-md-8">
        <input class="btn btn-primary" value="Save Changes" type="submit">
        <a href="{{ route(\App\Utilities\Constants::ROUTES_PROFILE, $username) }}">
            <input class="btn btn-default" value="Cancel" type="button">
        </a>
    </div>
</div>