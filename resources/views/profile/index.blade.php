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

                <div class="content-tabs card margin-5px">

                    <!-- Nav tabs -->

                    <ul class="nav nav-tabs" role="tablist">
                        <li class=" nav-item active" role="presentation">
                            <a href="#UserInfo" role="tab" data-toggle="tab">User Info</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#UserActivity" role="tab" data-toggle="tab">User Activity</a>
                        </li>
                        <li class="nav-item " role="presentation">
                            <a href="#problems" role="tab" data-toggle="tab">Problems</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#contests" role="tab" data-toggle="tab">Contests</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#groups" role="tab" data-toggle="tab">Groups</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#teams" role="tab" data-toggle="tab">Teams</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- User info tab -->
                        <div role="tabpanel" class="fade in tab-pane active " id="UserInfo">
                            <div class="container">
                              <div class="row">

                                  <!-- first column -->
                                  <div class="col-md-3">
                                    <img class="thumbnail img-responsive"
                                         src="{{ asset('images/'.$userData->profile_picture)}}" alt="profile pic"
                                         onerror=this.src="/images/profile/UserDefault.png"; width="200" height="300">

                                    <h3>{{ $userName }}</h3>
                                    @if(($userData->created_at)!= NULL)
                                    <h5><i class="fa fa-clock-o"></i> Joined {{ $date }}</h5>
                                    @endif
                                    <h4>Total Solved Problems <span class="label label-default">{{ $counter }}</span></h4>
                                    <br>
                                </div>

                                <!-- second column -->
                                <div class="col-md-5">
                                  <h2>Your Handles 
                                    @if((\Auth::user())!= null)
                                   @if((\Auth::user()->username)==$userName)
                                   <a href="/profile/edit">
                                      <span class="glyphicon glyphicon-plus-sign"></span>
                                  </a>
                                  @endif
                                  @endif
                                  </h2> <hr />
                                  <table class="table ">
                                    <thead>
                                      <tr>
                                        <th>Handle</th>
                                        <th>Judge</th>
                                    </tr>
                                </thead>

                                <tbody>
                                   @foreach($handle as $handle)
                                   <tr>
                                    <td>
                                        <h5>{{$handle['original']['pivot_handle']}}</h5>

                                    </td>
                                    <td>

                                       <a href="{{$handle['original']['link']}}">{{$handle['original']['name'] }}</a>
                                   </td>
                               </tr>

                               @endforeach 

                           </tbody>
                       </table>
                   </div>

               </div>
           </div>


       </div>

       <!-- Problems tab -->
       <div role="tabpanel" class="fade tab-pane " id="problems">

        <div class="panel-heading"><strong>Your wrong Answer Problems</strong></div>
        <div class="panel-body problems-panel-body">
            @include("problems.table")</div>
        </div>

        <!-- User activity tab -->
        <div role="tabpanel" class="fade tab-pane " id="UserActivity">
            {!! $chart->render() !!}
        </div>

        <!-- Contests tab -->
        <div role="tabpanel" class="fade tab-pane " id="contests">

          <div class="content-tabs card">

            <ul class="nav nav-tabs" role="tablist">
                <li class=" nav-item active" role="presentation">
                    <a href="#part" role="tab" data-toggle="tab">Your Participated Contests</a>
                </li>
                <li class=" nav-item " role="presentation">
                    <a href="#owned" role="tab" data-toggle="tab">Owned Contests</a>
                </li>
                <li class=" nav-item " role="presentation">
                    <a href="#admin" role="tab" data-toggle="tab">Contests you are admin in</a>
                </li>
            </ul>


            <div class="tab-content">


                <!-- patricipated contests -->
                <div role="tabpanel" class="fade in tab-pane active" id="part">
                   <div class="panel-body problems-panel-body">
                     @include('contests.contest_views.contests_table', ['contests' => $participatedContests, 'fragment' => ''])
                 </div>
             </div>


             <!-- owned contests -->
             <div role="tabpanel" class="fade tab-pane" id="owned">
                 @include('contests.contest_views.contests_table', ['contests' => $owned, 'fragment' => ''])
             </div>


             <!-- admin in contests -->
             <div role="tabpanel" class="fade tab-pane" id="admin">
                 @include('contests.contest_views.contests_table', ['contests' => $admin, 'fragment' => ''])
             </div>
         </div>
     </div>
 </div>


 <div role="tabpanel" class="fade tab-pane " id="groups">
    <div class="panel-heading"><strong>Your groups</strong></div>
    <div class="panel-body problems-panel-body">
      @include('groups.groups_table')</div>
  </div>
</div>

</div>
</div>
</div>
</div>
</div>
@endsection
