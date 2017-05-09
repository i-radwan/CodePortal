@php
    use App\Utilities\Constants;
    use Carbon\Carbon;

    $username = $user[Constants::FLD_USERS_USERNAME];
    $country= $user[Constants::FLD_USERS_COUNTRY];
    $profilePic = $user[Constants::FLD_USERS_PROFILE_PICTURE];
    $problemSolvedCount = count($solvedProblems);
    $fullName = $user[Constants::FLD_USERS_FIRST_NAME]."  ".$user[Constants::FLD_USERS_LAST_NAME];
    $email=$user[Constants::FLD_USERS_EMAIL];
    $joinDate = $user[Constants::FLD_CREATED_AT];
    $gender = $user[Constants::FLD_USERS_GENDER];
    $birthDate = $user[Constants::FLD_USERS_BIRTHDATE];
    $dt = Carbon::parse($birthDate);

    $birthDate=Carbon::createFromDate($dt->year)->diff(Carbon::now())->format('%y years');

    if($gender)
    {
        $gender="female";
    }
    else 
    {
        $gender="male";
    }

    if ($joinDate != null) {
        $joinDate = Carbon::parse($joinDate)->diffForHumans();
    }
@endphp

<div class="container">
    <div class="row">

        <!-- first column -->
        <div class="col-md-3">

            <!-- profile picture -->
            <img class="thumbnail img-responsive"
                 src="{{ asset('images/' . $profilePic) }}"
                 alt="profile pic"
                 width="200"
                 height="300"
                 onerror=this.src="/images/profile/UserDefault.png";>

            <!-- username with gender -->
            <h3>
                 {{ $username }}
                 <small>

                 ( {{$gender}} )

                 </small>
            </h3>

            <!-- Birthdate -->
            @if($birthDate > 12)
            <h4>
              <br>
             {{ $birthDate }} old
            </h4>
            @endif

            <!-- Joined since -->
            @if($joinDate != null)
                <h5><i class="fa fa-clock-o"></i> Joined {{ $joinDate }}</h5>
            @endif

            <!-- Country -->
            @if($country!= null)
            <p><i class="fa fa-map-marker"></i> {{$country}}</p>
            @endif

            <!-- Email -->
            @if(($email!=null)&& ($isAuth))
            <p><span class="glyphicon glyphicon-envelope"></span> {{$email}}</p>(not viewable)
            @endif


            <!-- Total solved problems -->
            <h4>
                Total Solved Problems
                <span class="label label-default">{{ $problemSolvedCount }}</span>
            </h4>
            <br>
        </div>


        <!-- second column -->
        <div class="col-md-5">

       <!--  adding full name if exist -->
        @if($fullName!= null)
            <h1>

            {{ $fullName}}

            </h1> 

        @endif

       <!--  adding edit handle icon -->
            <h2>
                Your Handles
                @if($isAuth)
                    <a href="{{ route(\App\Utilities\Constants::ROUTES_PROFILE_EDIT) }}">
                        <span class="glyphicon glyphicon-plus-sign"></span>
                    </a>
                @endif
            </h2>

            <hr/>

        <!-- adding handles if exist -->
            <table class="table">
                <thead>
                    <tr>
                        <th>Handle</th>
                        <th>Judge</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($user->handles()->get() as $handle)
                        @php
                            $handleName = $handle->pivot[\App\Utilities\Constants::FLD_USER_HANDLES_HANDLE];
                            $judgeName = $handle[\App\Utilities\Constants::FLD_JUDGES_NAME];
                            $judgeLink = $handle[\App\Utilities\Constants::FLD_JUDGES_LINK];
                        @endphp

                        <tr>
                            <td><h5>{{ $handleName }}</h5></td>
                            <td><a href="{{ $judgeLink }}">{{ $judgeName }}</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>