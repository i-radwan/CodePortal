@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="panel panel-default">
            @if(Auth::check())
                {{--Create new team button--}}
                <a href="{{ route(\App\Utilities\Constants::ROUTES_TEAMS_CREATE) }}"
                   class="btn btn-link text-dark pull-right margin-5px">
                    New
                </a>
            @endif

            <div class="panel-heading">Teams of {{ $user[\App\Utilities\Constants::FLD_USERS_USERNAME] }}</div>
            <div class="panel-body">
                @include('components.alert')

                @php($teams = $user->joiningTeams()->get())

                @if(count($teams) > 0)
                    @foreach($teams as $team)
                        @include('teams.team')
                    @endforeach
                @else
                    <h4 class="text-center margin-30px">
                        {{ $user[\App\Utilities\Constants::FLD_USERS_USERNAME] }} is not a member in any teams yet!
                    </h4>
                @endif
            </div>
        </div>
    </div>
    <span class="page-distinguishing-element" id="teams-page-hidden-element"></span>

@endsection
