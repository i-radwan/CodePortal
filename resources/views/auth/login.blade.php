@php($pageTitle = 'Code Portal | Log In')

@extends('layouts.app_container')

@section('content')
    @include('layouts.navbar')

    <div class="jumbotron login-cover">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-sm-6 login-cover-text-center-xs">
                    <h1><strong>{{ config('app.name') }}</strong></h1>
                    <h3>Practise Competitive Programming</h3>
                </div>

                <div class="col-md-4 col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">Login</div>
                        <div class="panel-body">
                            <form role="form" method="POST" action="{{ route(\App\Utilities\Constants::ROUTES_AUTH_LOGIN) }}">
                                {{ csrf_field() }}

                                {{-- Username --}}

                                <div class="form-group{{ $errors->has(\App\Utilities\Constants::FLD_USERS_USERNAME) ? ' has-error' : '' }} has-feedback">
                                    <input id="username" type="text" class="form-control"
                                           name="{{ \App\Utilities\Constants::FLD_USERS_USERNAME }}"
                                           value="{{ old(\App\Utilities\Constants::FLD_USERS_USERNAME) }}" placeholder="Username"
                                           required autofocus>
                                    <span class="glyphicon glyphicon-user form-control-feedback"
                                          aria-hidden="true"></span>

                                    @if ($errors->has(\App\Utilities\Constants::FLD_USERS_USERNAME))
                                        <span class="help-block">
                                            <strong>{{ $errors->first(\App\Utilities\Constants::FLD_USERS_USERNAME) }}</strong>
                                        </span>
                                    @endif
                                </div>

                                {{-- Password --}}

                                <div class="form-group{{ $errors->has(\App\Utilities\Constants::FLD_USERS_PASSWORD) ? ' has-error' : '' }} has-feedback">
                                    <input id="password" type="password" class="form-control"
                                           name="{{ \App\Utilities\Constants::FLD_USERS_PASSWORD }}" placeholder="Password" required>
                                    <span class="glyphicon glyphicon-lock form-control-feedback"
                                          aria-hidden="true"></span>

                                    @if ($errors->has(\App\Utilities\Constants::FLD_USERS_PASSWORD))
                                        <span class="help-block">
                                            <strong>{{ $errors->first(\App\Utilities\Constants::FLD_USERS_PASSWORD) }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox"
                                                   name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        Login
                                    </button>

                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        Forgot Your Password?
                                    </a>
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
