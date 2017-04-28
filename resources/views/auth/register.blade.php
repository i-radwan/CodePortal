@extends('layouts.app_container')

@section('content')
    @include('layouts.navbar')

    <div class="jumbotron signup-cover">
        <div class="container">
            <div class="row text-center">
                <h1><strong>{{ config('app.name') }}</strong></h1>
                <h3>Practise Competitive Programming</h3>
            </div>

            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <div class="panel panel-default">
                        <div class="panel-heading">Register</div>
                        <div class="panel-body">
                            <form class="form-horizontal" role="form" method="POST" action="{{ route(\App\Utilities\Constants::ROUTES_AUTH_REGISTER) }}">
                                {{ csrf_field() }}

                                {{-- Username --}}
                                <div class="form-group{{ $errors->has(\App\Utilities\Constants::FLD_USERS_USERNAME) ? ' has-error' : '' }} has-feedback">
                                    <label for="username" class="col-md-4 control-label">Username</label>

                                    <div class="col-md-6">
                                        <input id="username" type="text" class="form-control" name="{{ \App\Utilities\Constants::FLD_USERS_USERNAME }}" value="{{ old(\App\Utilities\Constants::FLD_USERS_USERNAME) }}" placeholder="Username" required>
                                        <span class="glyphicon glyphicon-user form-control-feedback" aria-hidden="true"></span>

                                        @if ($errors->has(\App\Utilities\Constants::FLD_USERS_USERNAME))
                                            <span class="help-block">
                                                <strong>{{ $errors->first(\App\Utilities\Constants::FLD_USERS_USERNAME) }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Email --}}
                                <div class="form-group{{ $errors->has(\App\Utilities\Constants::FLD_USERS_EMAIL) ? ' has-error' : '' }} has-feedback">
                                    <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                                    <div class="col-md-6">
                                        <input id="email" type="email" class="form-control" name="{{ \App\Utilities\Constants::FLD_USERS_EMAIL }}" value="{{ old(\App\Utilities\Constants::FLD_USERS_EMAIL) }}" placeholder="E-Mail Address" required>
                                        <span class="glyphicon glyphicon-envelope form-control-feedback" aria-hidden="true"></span>

                                        @if ($errors->has(\App\Utilities\Constants::FLD_USERS_EMAIL))
                                            <span class="help-block">
                                                <strong>{{ $errors->first(\App\Utilities\Constants::FLD_USERS_EMAIL) }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Password --}}
                                <div class="form-group{{ $errors->has(\App\Utilities\Constants::FLD_USERS_PASSWORD) ? ' has-error' : '' }} has-feedback">
                                    <label for="password" class="col-md-4 control-label">Password</label>

                                    <div class="col-md-6">
                                        <input id="password" type="password" class="form-control" name="{{ \App\Utilities\Constants::FLD_USERS_PASSWORD }}" placeholder="Password" required>
                                        <span class="glyphicon glyphicon-lock form-control-feedback" aria-hidden="true"></span>

                                        @if ($errors->has(\App\Utilities\Constants::FLD_USERS_PASSWORD))
                                            <span class="help-block">
                                                <strong>{{ $errors->first(\App\Utilities\Constants::FLD_USERS_PASSWORD) }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Password Confirmation --}}
                                <div class="form-group has-feedback">
                                    <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>

                                    <div class="col-md-6">
                                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password" required>
                                        <span class="glyphicon glyphicon-lock form-control-feedback" aria-hidden="true"></span>
                                    </div>
                                </div>

                                {{-- Codeforces handle--}}
                                <div class="form-group{{ $errors->has(\App\Utilities\Constants::FLD_USERS_CODEFORCES_HANDLE) ? ' has-error' : '' }}">
                                    <label for="codeforces-handle" class="col-md-4 control-label">Codeforces' Handle</label>

                                    <div class="col-md-6">
                                        <input id="codeforces-handle" type="text" class="form-control" name="{{ \App\Utilities\Constants::FLD_USERS_CODEFORCES_HANDLE }}" value="{{ old(\App\Utilities\Constants::FLD_USERS_CODEFORCES_HANDLE) }}" placeholder="Codeforces' Handle (Optional)">

                                        @if ($errors->has(\App\Utilities\Constants::FLD_USERS_CODEFORCES_HANDLE))
                                            <span class="help-block">
                                                <strong>{{ $errors->first(\App\Utilities\Constants::FLD_USERS_CODEFORCES_HANDLE) }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- UVA handle --}}
                                <div class="form-group{{ $errors->has(\App\Utilities\Constants::FLD_USERS_UVA_HANDLE) ? ' has-error' : '' }}">
                                    <label for="uva-handle" class="col-md-4 control-label">UVA's Handle</label>

                                    <div class="col-md-6">
                                        <input id="uva-handle" type="text" class="form-control" name="{{ \App\Utilities\Constants::FLD_USERS_UVA_HANDLE }}" value="{{ old(\App\Utilities\Constants::FLD_USERS_UVA_HANDLE) }}" placeholder="UVA's Handle (Optional)">

                                        @if ($errors->has(\App\Utilities\Constants::FLD_USERS_UVA_HANDLE))
                                            <span class="help-block">
                                                <strong>{{ $errors->first(\App\Utilities\Constants::FLD_USERS_UVA_HANDLE) }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Live Archive hanlde --}}
                                <div class="form-group{{ $errors->has(\App\Utilities\Constants::FLD_USERS_LIVE_ARCHIVE_HANDLE) ? ' has-error' : '' }}">
                                    <label for="live-archive-handle" class="col-md-4 control-label">Live Archive's Handle</label>

                                    <div class="col-md-6">
                                        <input id="live-archive-handle" type="text" class="form-control" name="{{ \App\Utilities\Constants::FLD_USERS_LIVE_ARCHIVE_HANDLE }}" value="{{ old(\App\Utilities\Constants::FLD_USERS_LIVE_ARCHIVE_HANDLE) }}" placeholder="Live Archive's Handle (Optional)">

                                        @if ($errors->has(\App\Utilities\Constants::FLD_USERS_LIVE_ARCHIVE_HANDLE))
                                            <span class="help-block">
                                                <strong>{{ $errors->first(\App\Utilities\Constants::FLD_USERS_LIVE_ARCHIVE_HANDLE) }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-4">
                                        <button type="submit" class="btn btn-primary">
                                            Register
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.footer')
@endsection
