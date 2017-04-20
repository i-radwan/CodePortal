@php
    $isMember = true;
@endphp

<div class="panel panel-default">
    {{--Check if authorized--}}
    @if($isMember)
        {{--Edit button--}}
        <a href="{{ url('teams/' . $team->id . '/edit')}}"
           class="btn btn-link text-dark pull-right margin-5px">
            Edit
        </a>

        {{--Delete button--}}
        <form action="{{ url('teams/' . $team->id)}}"
              method="POST">
            {{ method_field('DELETE') }}
            {{ csrf_field() }}

            <button onclick="return confirm('Are you sure want to delete the team?\nThis cannot be undone')"
                    type="submit"
                    class="btn btn-link text-dark pull-right margin-5px">
                Delete
            </button>
        </form>

        {{--Invite button--}}
        <a class="btn btn-link text-dark pull-right margin-5px">
            Invite
        </a>

        @include('teams.invite')
    @endif

    <div class="panel-heading">{{ $team->name }}</div>

    <table class="table table-bordered table-hover text-center">
        <thead>
        <tr>
            <th class="text-center">Username</th>
            <th class="text-center">E-Mail</th>
            <th class="text-center">Country</th>

            @if($isMember)
                <th class="text-center">Action</th>
            @endif
        </tr>
        </thead>

        <tbody>
        @foreach($team->members()->get() as $member)
            <tr>
                {{--Username--}}
                <td><a href="{{ url('profile/' . $member->username) }}">{{ $member->username }}</a></td>

                {{--E-Mail--}}
                <td>{{ $member->email }}</td>

                {{--User country--}}
                <td>{{ $member->country }}</td>

                @if($isMember)
                    <td>
                        {{--Remove member button--}}
                        <form action="{{ url('teams/' . $team->id . '/remove/' . $member->id)}}"
                              method="POST">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}

                            <button onclick="return confirm('Are you sure want to remove {{ $member->username }} from the team?')"
                                    type="submit"
                                    class="btn-link text-dark">
                                Remove
                            </button>
                        </form>
                    </th>
                @endif
            </tr>
        @endforeach

        {{--TODO: add invitations--}}
        {{--@foreach($team->invitedUsers()->get() as $user)--}}
            {{--<tr>--}}
                {{--Username--}}
                {{--<td><a href="{{ url('profile/' . $user->username) }}">{{ $user->username }}</a></td>--}}

                {{--E-Mail--}}
                {{--<td>{{ $user->email }}</td>--}}

                {{--User country--}}
                {{--<td>{{ $user->country }}</td>--}}

                {{--@if($isMember)--}}
                    {{--<td>--}}
                        {{--Cancel invitation button--}}
                        {{--<form action="{{ url('teams/' . $team->id . '/remove/' . $member->id)}}"--}}
                              {{--method="POST">--}}
                            {{--{{ method_field('DELETE') }}--}}
                            {{--{{ csrf_field() }}--}}

                            {{--<button onclick="return confirm('Are you sure want to remove {{ $member->username }} from the team?')"--}}
                                    {{--type="submit"--}}
                                    {{--class="btn-link text-dark">--}}
                                {{--Cancel--}}
                            {{--</button>--}}
                        {{--</form>--}}
                    {{--</th>--}}
                {{--@endif--}}
            {{--</tr>--}}
        {{--@endforeach--}}
        </tbody>
    </table>
</div>
