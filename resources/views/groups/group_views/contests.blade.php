{{--Display single group contests info--}}
<div class="text-center">
    <table class="table table-bordered" id="contests_table">
        <thead>
        <tr>
            <th class="text-center">ID</th>
            <th class="text-center contest-table-name-th">Name</th>
            <th class="text-center">Time</th>
            <th class="text-center">Duration</th>
        </tr>
        </thead>
        <tbody>
        @foreach($data[Constants::CONTESTS_CONTESTS_KEY] as $contest)
            <tr>
                <td>{{ $contest->id }}</td>
                <td>
                    <a href="{{ url('contest/' . $contest->id) }}">
                        {{ $contest->name }}
                    </a>
                </td>
                <td>{{ date('D M d, H:i', strtotime($contest->time))}}</td>
                <td>{{ \App\Utilities\Utilities::convertMinsToHoursMins($contest->duration) }} hrs</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{--Pagination--}}
    {{ $data[Constants::CONTESTS_CONTESTS_KEY]->fragment('contests')->links() }}
</div>