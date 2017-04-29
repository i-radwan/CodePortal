@php($pageTitle = 'Code Portal | Forget Password')

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
                        <div class="panel-heading">Reset Password</div>
                        <div class="panel-body">
                            @if (session('status'))
                                <div class="alert alert-success">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <form role="form" method="POST" action="{{ route('password.email') }}">
                                {{ csrf_field() }}

                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }} has-feedback">
                                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="E-Mail Address" required>
                                    <span class="glyphicon glyphicon-envelope form-control-feedback" aria-hidden="true"></span>

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        Send Password Reset Link
                                    </button>
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
