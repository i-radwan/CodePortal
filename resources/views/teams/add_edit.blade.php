@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $actionTitle }}</div>
            <div class="panel-body">
                <form class="form-horizontal" role="form" method="POST" action="{{ $actionUrl }}">
                    {{ csrf_field() }}

                    {{-- Name --}}
                    <div class="form-group{{ $errors->has(\App\Utilities\Constants::FLD_TEAMS_NAME) ? ' has-error' : '' }}">
                        <label for="name" class="col-md-4 control-label">Name</label>

                        <div class="col-md-6">
                            <input id="name" type="text" class="form-control"
                                   name="{{ \App\Utilities\Constants::FLD_TEAMS_NAME }}"
                                   value="{{ old(\App\Utilities\Constants::FLD_TEAMS_NAME, $teamName) }}"
                                   placeholder="Name"
                                   required>

                            @if ($errors->has(\App\Utilities\Constants::FLD_TEAMS_NAME))
                                <span class="help-block">
                                    <strong>{{ $errors->first(\App\Utilities\Constants::FLD_TEAMS_NAME) }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    {{--Submit Button--}}
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                                {{ $actionBtnTitle }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
