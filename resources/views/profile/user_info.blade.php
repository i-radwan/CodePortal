@php
    use App\Utilities\Constants;
    use Carbon\Carbon;

    $username = $user[Constants::FLD_USERS_USERNAME];
    $profilePic = $user[Constants::FLD_USERS_PROFILE_PICTURE];
    $problemSolvedCount = count($solvedProblems);

    $joinDate = $user[Constants::FLD_CREATED_AT];

    if ($joinDate != null) {
        $joinDate = Carbon::parse($joinDate)->diffForHumans();
    }
@endphp

<div class="container">
    <div class="row">
        <!-- first column -->
        <div class="col-md-3">
            <img class="thumbnail img-responsive"
                 src="{{ asset('images/' . $profilePic) }}"
                 alt="profile pic"
                 width="200"
                 height="300"
                 onerror=this.src="/images/profile/UserDefault.png";>

            <h3>{{ $username }}</h3>

            @if($joinDate != null)
                <h5><i class="fa fa-clock-o"></i> Joined {{ $joinDate }}</h5>
            @endif

            <h4>
                Total Solved Problems
                <span class="label label-default">{{ $problemSolvedCount }}</span>
            </h4>

            <br>
        </div>

        <!-- second column -->
        <div class="col-md-5">
            <h2>
                Your Handles
                @if($isAuth)
                    <a href="{{ route(\App\Utilities\Constants::ROUTES_PROFILE_EDIT) }}">
                        <span class="glyphicon glyphicon-plus-sign"></span>
                    </a>
                @endif
            </h2>

            <hr/>

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