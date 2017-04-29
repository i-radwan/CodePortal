{{--Display single group members info--}}
<table class="table table-bordered table-hover text-center">
    <thead>
    <tr>
        <th class="text-center" width="33%">Username</th>
        <th class="text-center" width="33%">Email</th>
        <th class="text-center">Country</th>
        @if($isOwner)
            <th class="text-center">Actions</th>
        @endif
    </tr>
    </thead>
    <tbody>
    @foreach($members as $member)
        @php
            $memberID = $member[\App\Utilities\Constants::FLD_USERS_ID];
            $memberUsername = $member[\App\Utilities\Constants::FLD_USERS_USERNAME];
            $memberEmail= $member[\App\Utilities\Constants::FLD_USERS_EMAIL];
            $memberCountry = $member[\App\Utilities\Constants::FLD_USERS_COUNTRY];
        @endphp
        <tr>
            {{--Username--}}
            <td>
                <a href="{{ route(\App\Utilities\Constants::ROUTES_PROFILE, $memberUsername) }}">
                    {{ $memberUsername }}
                </a>
            </td>

            {{--Email--}}
            <td> {{ $memberEmail }}</td>

            {{--Country--}}
            <td> {{ $memberCountry }}</td>

            {{--Actions--}}
            @if($isOwner)
                <td class="text-center">

                    {{--Remove member--}}
                    @include('components.action_form', ['url' => route(\App\Utilities\Constants::ROUTES_GROUPS_MEMBER_REMOVE, [$groupID, $memberID]), 'method' => 'DELETE', 'confirm' => true, 'confirmMsg' => "'Are you sure want to remove this member? This action cannot be undone!'", 'btnIDs' => "testing-remove-member-$memberID", 'btnClasses' => 'btn btn-link text-dark', 'btnTxt' => 'Remove'])

                </td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>
