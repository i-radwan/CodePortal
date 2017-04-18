

{!! Charts::assets() !!}
@extends('layouts.app')

@section('content')

<div class="container">
  <div class="row">
    <div class="col-md-8">
      <div class="panel panel-default">
        <div class="panel-heading">Profile</div>
        @if((\Auth::user()->username)==$userName)


        <a href="{{ url('edit') }}" class="btn btn-link pull-right btn-sm RbtnMargin" role="button">edit
          <i class="fa fa-gear"></i> </a>


          @endif
          <div class="container">
           <br> <img class="thumbnail img-responsive"
           src="{{ asset('images/'.$userData->profile_picture)}}"  alt="profile pic"
           onerror=this.src="/images/profile/UserDefault.png" width="200" height="300"> 
         </div>
         <div class="panel-body">
           <h3>{{ $userName }}</h3>   
           <h5><i class="fa fa-clock-o"></i> Joined {{ $date }}</h5>
           <h4>Total Solved Problems <span class="label label-default">{{ $counter }}</span></h4>
         </div>
       </div>
     </div>
     <div class="col-md-4">
      For Notification
    </div>
  </div>
  <div class="row">
    <div class="col-md-8">
      {!! $chart->render() !!}


    </div>
    <div class="col-md-4 ">
      <div class="panel panel-default">
        <div class="panel-heading">Problems to solve</div>
        <div class="panel-body problems-panel-body">
          @include("problems.table")</div>
        </div>
      </div>
    </div>
  </div>
  @endsection
