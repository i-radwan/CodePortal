{{--Display single group members info--}}
<table class="table table-bordered table-hover text-center">
    <thead>
    <tr>
        <th class="text-center" width="33%">Username</th>
        <th class="text-center" width="33%">Email</th>
        <th class="text-center">Country</th>
    </tr>
    </thead>
    <tbody>
    @foreach($admins as $admin)
        @php
            $adminID = $admin[\App\Utilities\Constants::FLD_USERS_ID];
            $adminUsername = $admin[\App\Utilities\Constants::FLD_USERS_USERNAME];
            $adminEmail= $admin[\App\Utilities\Constants::FLD_USERS_EMAIL];
            $adminCountry = $admin[\App\Utilities\Constants::FLD_USERS_COUNTRY];
        @endphp
        <tr>
            {{--Username--}}
            <td>
                <a href="{{ route(\App\Utilities\Constants::ROUTES_PROFILE, $adminUsername) }}">
                    {{ $adminUsername }}
                </a>
            </td>

            {{--Email--}}
            <td> {{ $adminEmail }}</td>

            {{--Country--}}
            <td> {{ $adminCountry }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
