@extends('layouts.app_container')

@section('content')
    <div class="space-wrapper login-page">
        @include('layouts.navbar')

        <div class="container">
            <div class="row">
                <div class="col-md-8 col-sm-6">
                    <h1><strong>{{ config('app.name') }}</strong></h1>
                    <h3>Practise Competitive Programming</h3>
                </div>

                <div class="col-md-4 col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">Login</div>
                        <div class="panel-body">
                            <form role="form" method="POST" action="{{ route('login') }}">
                                {{ csrf_field() }}

                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }} has-feedback">
                                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="E-Mail Address" required autofocus>
                                    <span class="glyphicon glyphicon-user form-control-feedback" aria-hidden="true"></span>

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }} has-feedback">
                                    <input id="password" type="password" class="form-control" name="password" placeholder="Password" required>
                                    <span class="glyphicon glyphicon-lock form-control-feedback" aria-hidden="true"></span>

                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
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
