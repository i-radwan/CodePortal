{{--Display single contest participants info--}}
<table class="table table-bordered table-hover text-center">
    <thead>
    <tr>
        <th class="text-center" width="30%">Username</th>
        <th class="text-center" width="30%">Email</th>
        <th class="text-center">Country</th>
    </tr>
    </thead>
    <tbody>
    @foreach($members as $member)
        <tr>
            <td>
                <a href="{{ url('profile/' . $member[Constants::FLD_USERS_USERNAME]) }}">
                    {{ $member[Constants::FLD_USERS_USERNAME] }}
                </a>
            </td>
            <td> {{ $member[Constants::FLD_USERS_EMAIL] }}</td>
            <td> {{ $member[Constants::FLD_USERS_COUNTRY] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
