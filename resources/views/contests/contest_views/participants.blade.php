{{--Display single contest participants info--}}
<table class="table table-bordered table-hover text-center">
    <thead>
        <tr>
            <th class="text-center" width="50%">Username</th>
            <th class="text-center">Country</th>
        </tr>
    </thead>
    <tbody>
    @foreach ( $data[Constants::SINGLE_CONTEST_PARTICIPANTS_KEY] as $participant)
        <tr>
            <td>
                <a href="{{url('profile/'.$participant[Constants::FLD_USERS_USERNAME])}}">{{$participant[Constants::FLD_USERS_USERNAME]}}</a>
            </td>
            <td>{{$participant[Constants::FLD_USERS_COUNTRY]}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
