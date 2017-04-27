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
        <tr>

            {{--Username--}}
            <td>
                <a href="{{ url('profile/' . $seeker->username) }}">
                    {{ $seeker->username }}
                </a>
            </td>

            {{--Email--}}
            <td> {{ $seeker->email }}</td>

            {{--Action : Accept/Reject--}}
            <td>
                {{--Accept Form--}}
                @include('components.action_form', ['url' => url('group/request/accept/'.$groupID.'/'.$seeker->id), 'method' => 'PUT', 'confirm' => false, 'btnClasses' => 'btn btn-link text-dark', 'btnIDs' => "testing-accept-request-$seeker->id", 'btnTxt' => 'Accept'])

                {{--Reject Form--}}
                @include('components.action_form', ['url' => url('group/request/reject/'.$groupID.'/'.$seeker->id), 'method' => 'PUT', 'confirm' => false, 'btnClasses' => 'btn btn-link text-dark', 'btnIDs' => "testing-reject-request-$seeker->id", 'btnTxt' => 'Reject'])

            </td>
        </tr>
    @endforeach
    </tbody>
</table>
