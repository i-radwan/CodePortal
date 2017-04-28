{{--Display single contest participants info--}}
<table class="table table-bordered table-hover text-center">
    <thead>
    <tr>
        <th class="text-center" width="33%">Username</th>
        <th class="text-center" width="33%">Email</th>
        <th class="text-center">Actions</th>
    </tr>
    </thead>
    <tbody>
    @foreach($seekers as $seeker)
        @php
            $seekerID = $seeker[\App\Utilities\Constants::FLD_USERS_ID];
            $seekerUsername = $seeker[\App\Utilities\Constants::FLD_USERS_USERNAME];
            $seekerEmail = $seeker[\App\Utilities\Constants::FLD_USERS_EMAIL]
        @endphp
        <tr>

            {{--Username--}}
            <td>
                <a href="{{ url('profile/' . $seekerUsername) }}">
                    {{ $seekerUsername }}
                </a>
            </td>

            {{--Email--}}
            <td> {{ $seekerEmail }}</td>

            {{--Action : Accept/Reject--}}
            <td>
                {{--Accept Form--}}
                @include('components.action_form', ['halfWidth' => true, 'url' => url('group/request/accept/' . $groupID . '/' . $seekerID), 'method' => 'PUT', 'confirm' => false, 'btnClasses' => 'btn btn-link text-dark', 'btnIDs' => "testing-accept-request-$seekerID", 'btnTxt' => 'Accept'])

                {{--Reject Form--}}
                @include('components.action_form', ['halfWidth' => true, 'url' => url('group/request/reject/' . $groupID . '/' . $seekerID), 'method' => 'PUT', 'confirm' => false, 'btnClasses' => 'btn btn-link text-dark', 'btnIDs' => "testing-reject-request-$seekerID", 'btnTxt' => 'Reject'])

            </td>
        </tr>
    @endforeach
    </tbody>
</table>
