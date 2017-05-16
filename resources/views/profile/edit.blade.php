@php
    use App\Utilities\Constants;

    $userID = $user[Constants::FLD_USERS_ID];
    $username = $user[Constants::FLD_USERS_USERNAME];
    $email = $user[Constants::FLD_USERS_EMAIL];
    $firstName = isset($user[Constants::FLD_USERS_FIRST_NAME]) ? $user[Constants::FLD_USERS_FIRST_NAME]: "";
    $lastName = isset($user[Constants::FLD_USERS_LAST_NAME]) ? $user[Constants::FLD_USERS_LAST_NAME]: "";
    $country = isset($user[Constants::FLD_USERS_COUNTRY]) ? $user[Constants::FLD_USERS_COUNTRY]: "";
    $birthDate = isset($user[Constants::FLD_USERS_BIRTHDATE]) ? $user[Constants::FLD_USERS_BIRTHDATE]: "";
    $userPicture = isset($user[Constants::FLD_USERS_PROFILE_PICTURE]) ? $user[Constants::FLD_USERS_PROFILE_PICTURE]: "";

    $codeforcesHandle = $user->handle(Constants::JUDGE_CODEFORCES_ID);
    $uvaHandle = $user->handle(Constants::JUDGE_UVA_ID);
    $liveArchiveHandle = $user->handle(Constants::JUDGE_LIVE_ARCHIVE_ID);

    $userCountry = isset($user[Constants::FLD_USERS_COUNTRY]) ? $user[Constants::FLD_USERS_COUNTRY] : "";
    $handle = $user->handles()->get();
@endphp


@extends('layouts.app')
@section('content')
    <div class="container">

        {{--Page header--}}
        <h1>Edit Profile</h1>
        <hr/>

        {{--Alerts--}}
        @include('components.alert')

        <form method="post" class="form-horizontal" role="form"
              action="{{ route(\App\Utilities\Constants::ROUTES_PROFILE_UPDATE) }}" enctype="multipart/form-data">

            {{--Hidden fields--}}
            {{ csrf_field() }}
            <input type="hidden" name="id" value="{{ $userID }}">

            <div class="row">
                {{--Photo column--}}
                <div class="col-md-3 text-center">
                    <div class="text-center">

                        {{--Photo--}}
                        <img src="{{ asset('profile_pics/' . $userPicture)}}" class="avatar img-circle"
                             onerror="this.src='/images/profile/UserDefault.png';" width="200" height="200"
                             alt="avatar">

                        {{--Button--}}
                        <span class="btn btn-primary btn-file">
                            Browse...<input type="file" name="profile_picture">
                        </span>
                        <p class="text-dark small margin-5px" id="profile-pic-name"></p>
                    </div>
                </div>

                {{--Personal info column--}}
                <div class="col-md-9 personal-info">
                    @include('profile.edit_form_data')
                </div>

            </div>
        </form>
    </div>
@endsection

