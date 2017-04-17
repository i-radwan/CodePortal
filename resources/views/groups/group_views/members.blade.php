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
        <tr>
            <td>
                <a href="{{ url('profile/' . $member[Constants::FLD_USERS_USERNAME]) }}">
                    {{ $member[Constants::FLD_USERS_USERNAME] }}
                </a>
            </td>
            <td> {{ $member[Constants::FLD_USERS_EMAIL] }}</td>
            <td> {{ $member[Constants::FLD_USERS_COUNTRY] }}</td>
            @if($isOwner)
                <td class="text-center">
                    <form action="{{url('group/member/'.$groupID.'/'.$member[Constants::FLD_USERS_ID])}}"
                          method="post" class="action">
                        {{method_field('DELETE')}}
                        {{csrf_field()}}
                        <button type="submit" class="btn btn-link text-dark"
                                onclick="return confirm('Are you sure want to remove this member?\nThis cannot be undone')">
                            Remove
                        </button>
                    </form>
                </td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>
