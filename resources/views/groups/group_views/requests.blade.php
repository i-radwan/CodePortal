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
            <td>
                <a href="{{ url('profile/' . $seeker->username) }}">
                    {{ $seeker->username }}
                </a>
            </td>
            <td> {{ $seeker->email }}</td>
            <td>
                <form action="{{url('group/request/accept/'.$groupID.'/'.$seeker->id)}}"
                      method="post" class="action">
                    {{method_field('PUT')}}
                    {{csrf_field()}}
                    <button type="submit" class="btn btn-link text-dark">Accept
                    </button>
                </form>
                <form action="{{url('group/request/reject/'.$groupID.'/'.$seeker->id)}}"
                      method="post" class="action">
                    {{method_field('PUT')}}
                    {{csrf_field()}}
                    <button type="submit" class="btn btn-link text-dark">Reject
                    </button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
