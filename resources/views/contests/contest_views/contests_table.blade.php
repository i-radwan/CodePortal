@if(count($contests))
    <table class="table table-bordered table-hover" id="contests_table">
        <thead>
        <tr>
            <th class="text-center">ID</th>
            <th class="text-center contest-table-name-th">Name</th>
            <th class="text-center">Time</th>
            <th class="text-center">Duration</th>
            @if(!isset($isGroup))
                <th class="text-center">Owner</th>
            @endif
        </tr>
        </thead>

        <tbody>
        @foreach($contests as $contest)
            @php
                $contestID = $contest[\App\Utilities\Constants::FLD_CONTESTS_ID];
                $contestName = $contest[\App\Utilities\Constants::FLD_CONTESTS_NAME];
                $contestTime  =  date('D M d, H:i', strtotime($contest[\App\Utilities\Constants::FLD_CONTESTS_TIME]));
                $contestDuration = \App\Utilities\Utilities::convertSecondsToDaysHoursMins($contest[\App\Utilities\Constants::FLD_CONTESTS_DURATION]);
                $contestOwnerUsername = $contest->owner[\App\Utilities\Constants::FLD_USERS_USERNAME];
            @endphp

            <tr>
                {{--ID--}}
                <td class="testing-contest-id-cell">{{ $contestID }}</td>

                {{--Name--}}
                <td>
                    <a href="{{ route(\App\Utilities\Constants::ROUTES_CONTESTS_DISPLAY, $contestID) }}">
                        {{ $contestName }}
                    </a>
                </td>

                {{--Time--}}
                <td>{{ $contestTime }}</td>

                {{--Duration--}}
                <td>{{ $contestDuration }}</td>

                {{--Owner--}}
                @if(!isset($isGroup))
                    <td>
                        <a href="{{ route(\App\Utilities\Constants::ROUTES_PROFILE, $contestOwnerUsername) }}">
                            {{ $contestOwnerUsername }}
                        </a>
                    </td>
                @endif
            </tr>
        @endforeach
        </tbody>
    </table>

    {{--Pagination--}}
    @if(!isset($isGroup))
        {{ $contests->appends(Request::all())->fragment($fragment)->render() }}
    @else
        {{ $contests->fragment('contests')->links() }}
    @endif

@else
    <p class="margin-30px">No contests!</p>
@endif
