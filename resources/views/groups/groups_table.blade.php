@if($groups->count())
    <table class="table table-bordered">
        <thead>
        <tr>
            <th class="text-center">ID</th>
            <th class="text-center group-table-name-th">Name</th>
            <th class="text-center">Owner</th>
        </tr>
        </thead>
        <tbody>
        @foreach($groups as $group)
            @php
                $groupID = $group[\App\Utilities\Constants::FLD_GROUPS_ID];
                $groupName = $group[\App\Utilities\Constants::FLD_GROUPS_NAME];
                $groupOwnerUsername = $group->owner[\App\Utilities\Constants::FLD_USERS_USERNAME];
            @endphp
            <tr>

                {{--Group ID--}}
                <td>{{ $groupID }}</td>

                {{--Group name and link (for users only)--}}
                <td>

                    {{-- Only singed in users can see group details --}}
                    @if(Auth::check())
                        <a href="{{ route(\App\Utilities\Constants::ROUTES_GROUPS_DISPLAY, $groupID) }}">
                            {{ $groupName }}
                        </a>
                    @else
                        {{ $groupName }}
                    @endif

                </td>

                {{--Owner Username--}}
                <td>
                    <a href="{{ route(\App\Utilities\Constants::ROUTES_PROFILE, $groupOwnerUsername) }}">
                        {{ $groupOwnerUsername }}
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>


    {{--Pagination--}}
    {{ $groups->appends(Request::all())->render() }}

@else
    <p class="margin-30px">No groups!</p>
@endif