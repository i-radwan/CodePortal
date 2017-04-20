<div class="panel panel-default">
    {{--Check if authorized--}}
    @if(true)
        {{--Edit button--}}
        <a href="{{ url('teams/' . $team->id . '/edit')}}"
           class="btn btn-link text-dark pull-right margin-5px">
            Edit
        </a>

        {{--Delete button--}}
        <form action="{{ url('teams/' . $team->id)}}"
              method="post">
            {{ method_field('DELETE') }}
            {{ csrf_field() }}

            <button onclick="return confirm('Are you sure want to delete the team?\nThis cannot be undone')"
                    type="submit"
                    class="btn btn-link text-dark pull-right margin-5px">
                Delete
            </button>
        </form>
    @endif

    <div class="panel-heading">{{ $team->name }}</div>

    <table class="table table-bordered table-hover text-center">
        <thead>
        <tr>
            <th class="text-center" width="50%">Username</th>
            <th class="text-center">Country</th>
        </tr>
        </thead>

        <tbody>
        @foreach($team->members()->get() as $member)
            <tr>
                {{--Username--}}
                <td><a href="{{ url('profile/' . $member->username) }}">{{ $member->username }}</a></td>

                {{--User country--}}
                <td>{{ $member->country }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
