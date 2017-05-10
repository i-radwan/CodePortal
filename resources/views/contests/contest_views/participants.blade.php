{{--Display single contest participants info--}}
<table class="table table-bordered table-hover text-center">
    <thead>
    <tr>
        <th class="text-center" width="50%">Username</th>
        <th class="text-center">Country</th>

        @if($isOwnerOrOrganizer)
            <th class="text-center">Actions</th>
        @endif
    </tr>
    </thead>
    <tbody>
    @foreach($participants as $participant)
        @php
            $userID = $participant[\App\Utilities\Constants::FLD_USERS_ID];
            $userName = $participant[\App\Utilities\Constants::FLD_USERS_USERNAME];
            $country = $participant[\App\Utilities\Constants::FLD_USERS_COUNTRY];
        @endphp

        <tr>
            {{--Username--}}
            <td><a class="testing-participant-username"
                   href="{{ route(\App\Utilities\Constants::ROUTES_PROFILE, $userName) }}">{{ $userName }}</a></td>

            {{--User country--}}
            <td>{{ $country }}</td>

            {{--Actions--}}
            @if($isOwnerOrOrganizer)

                {{--Remove participant--}}
                <td class="text-center">
                    @include('components.action_form', ['url' => route(\App\Utilities\Constants::ROUTES_CONTESTS_PARTICIPANTS_DELETE, [$contestID, $userName]), 'method' => 'DELETE', 'confirm' => true, 'confirmMsg' => "'Are you sure want to remove this participant? This action cannot be undone!'", 'btnIDs' => "", 'btnClasses' => 'btn btn-link text-dark', 'btnTxt' => 'Remove'])
                </td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>

{{--Pagination--}}
{{ $participants->appends(Request::all())->render() }}