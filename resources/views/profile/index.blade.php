

{!! Charts::assets() !!}
@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-7">
      <div class="panel panel-default">
        <div class="panel-heading">Profile <a class=" pull-right btn-sm RbtnMargin"href="/status/abzo/">History of submissions</a> </div>
        @if((\Auth::user()->username)==$userName)

        <a href="{{ url('edit') }}" class="btn btn-link pull-right btn-sm RbtnMargin"role="button">edit <i class="fa fa-gear"></i> </a>


        @endif
        <div class="container">
         <br> <img class="thumbnail img-responsive"src="/images/profile/UserDefault.png" class="img-rounded" alt="Cinque Terre" width="200" height="240"> 
       </div>
       <div class="panel-body">
         <h3>{{ $userName }}</h3>   
         <h5><i class="fa fa-clock-o"></i> Joined {{ $date }}</h5>
         <h4>Problems Solved  <span class="label label-default">{{ $counter }}</span></h4>
       </div>
     </div>
   </div>
   <div class="col-md-5">
    FOR NOTIFICATION
  </div>
</div>
<div class="row">
  <div class="col-md-7">
    FOR STATISTICS
    {!! $chart->render() !!}

  </div>
  <div class="col-md-5 ">
    <div class="panel panel-default">
      <div class="panel-heading">Problems to solve</div>
      <div class="panel-body problems-panel-body">
        @include("problems.table")</div>
      </div>
    </div>
  </div>
</div>
@endsection
