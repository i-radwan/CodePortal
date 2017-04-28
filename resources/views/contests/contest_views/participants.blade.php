{{--Display single contest participants info--}}
<table class="table table-bordered table-hover text-center">
    <thead>
        <tr>
            <th class="text-center" width="50%">Username</th>
            <th class="text-center">Country</th>
        </tr>
    </thead>
    <tbody>
        @foreach($participants as $participant)
            @php
                $userName = $participant[\App\Utilities\Constants::FLD_USERS_USERNAME];
                $country = $participant[\App\Utilities\Constants::FLD_USERS_COUNTRY];
            @endphp

            <tr>
                {{--Username--}}
                <td><a class="testing-participant-username" href="{{ route(\App\Utilities\Constants::ROUTES_PROFILE, $userName) }}">{{ $userName }}</a></td>

                {{--User country--}}
                <td>{{ $country }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

{{--Pagination--}}
{{ $participants->appends(Request::all())->render() }}