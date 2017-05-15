@php
    use App\Utilities\Constants;
    use Carbon\Carbon;

    $username = $user[Constants::FLD_USERS_USERNAME];
    $email = $user[Constants::FLD_USERS_EMAIL];
    $fullName = $user[Constants::FLD_USERS_FIRST_NAME]."  ".$user[Constants::FLD_USERS_LAST_NAME];
    $country= $user[Constants::FLD_USERS_COUNTRY];
    $profilePic = $user[Constants::FLD_USERS_PROFILE_PICTURE];
    $joinDate = $user[Constants::FLD_CREATED_AT];
    $gender = $user[Constants::FLD_USERS_GENDER];
    $problemSolvedCount = $solvedProblems->total();

    $birthDate = Carbon::parse($user[Constants::FLD_USERS_BIRTHDATE]);
    $userAge = Carbon::createFromDate($birthDate->year)->diff(Carbon::now())->format('%y');

    if($gender == Constants::GENDER_FEMALE)
        $gender="female";
    else
        $gender="male";

    if ($joinDate != null)
        $joinDate = Carbon::parse($joinDate)->diffForHumans();
@endphp

<div class="row">

    {{--Details Column--}}
    <div class="col-md-8">

        {{--FullName--}}
        @if($fullName!= null)
            <h3>
                {{ $fullName}}
            </h3>
        @endif

        {{--Username, gender--}}
        <p class="row">
            <strong class="col-sm-5">Username: </strong>
            {{ $username }}
            <small>
                ( {{$gender}} )
            </small>
        </p>

        {{--Email--}}
        @if(($email != null) && ($isProfileOwner))
            <p class="row">
                <strong class="col-sm-5">E-mail: </strong>
                {{ $email }}
            </p>
        @endif

        {{--Age--}}
        @if($userAge > 10)
            <p class="row">
                <strong class="col-sm-5">Age: </strong>
                {{ $userAge }} yrs old
            </p>
        @endif

        {{--Member since--}}
        @if($joinDate != null)
            <p class="row">
                <strong class="col-sm-5">Joined: </strong>
                {{ $joinDate }}
            </p>
        @endif

        {{--Country--}}
        @if($country!= null)
            <p class="row">
                <strong class="col-sm-5">Country: </strong>
                {{ $country }}
            </p>
        @endif

        {{--Total solved problems count--}}
        <p class="row">
            <strong class="col-sm-5">Solved: </strong>
            {{ $problemSolvedCount }} Problem(s)
        </p>

        {{--Handles--}}
        <br/>
        <p>
            <strong class="text-dark">Handles: </strong>
        </p>
        @foreach($user->handles()->get() as $handle)
            @php
                $handleName = $handle->pivot[\App\Utilities\Constants::FLD_USER_HANDLES_HANDLE];
                $judgeName = $handle[\App\Utilities\Constants::FLD_JUDGES_NAME];
                $judgeLink = $handle[\App\Utilities\Constants::FLD_JUDGES_LINK];
            @endphp

            <p>{{ $handleName }}
                <small>(<a href="{{ $judgeLink }}">{{ $judgeName }}</a>)</small>
            </p>
        @endforeach

        {{--Add new handle--}}
        @if($isProfileOwner)
            <small>
                <a href="{{ route(\App\Utilities\Constants::ROUTES_PROFILE_EDIT) }}">
                    + Add
                </a>
            </small>
        @endif
    </div>

    {{--Photo column--}}
    <div class="col-md-4">

        {{--Profile Picture--}}
        <img class="thumbnail img-responsive pull-right margin-10px"
             src="{{ asset('images/' . $profilePic) }}"
             alt="profile pic"
             width="200"
             height="300"
             onerror="this.src='/images/profile/UserDefault.png';"/>
    </div>
</div>
