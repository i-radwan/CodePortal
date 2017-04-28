{!! Charts::assets() !!}
@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">Profile</div>

                    @if((\Auth::user())!= null)
                        @if((\Auth::user()->username)==$userName)


                            <a href="{{ route(\App\Utilities\Constants::ROUTES_PROFILE_EDIT) }}" class="btn btn-link pull-right btn-sm RbtnMargin "
                               role="button">edit
                                <i class="fa fa-gear"></i> </a>
                        @endif
                    @endif

                    <div class="panel-body custom-panel">


                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li class=" nav-item active" role="presentation">
                                <a href="#UserInfo" role="tab" data-toggle="tab">User Info</a>
                            </li>
                            <li class="nav-item " role="presentation">
                                <a href="#problems" role="tab" data-toggle="tab">Problems</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#UserActivity" role="tab" data-toggle="tab">User Activity</a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <!-- User info tab -->
                            <div role="tabpanel" class="fade in tab-pane active " id="UserInfo">

                                <br> <img class="thumbnail img-responsive"
                                          src="{{ asset('images/'.$userData->profile_picture)}}" alt="profile pic"
                                          onerror=this.src="/images/profile/UserDefault.png" width="200" height="300">

                                <h3>{{ $userName }}</h3>
                                @if(($userData->created_at)!= NULL)
                                    <h5><i class="fa fa-clock-o"></i> Joined {{ $date }}</h5>
                                @endif
                                <h4>Total Solved Problems <span class="label label-default">{{ $counter }}</span></h4>
                                <br>

                            </div>

                            <!-- Problems tab -->
                            <div role="tabpanel" class="fade tab-pane " id="problems">

                                <div class="panel-heading">Problems to solve</div>
                                <div class="panel-body problems-panel-body">
                                    @include("problems.table")</div>
                            </div>

                            <!-- User activity tab -->
                            <div role="tabpanel" class="fade tab-pane " id="UserActivity">
                                <br>
                                {!! $chart->render() !!}
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
