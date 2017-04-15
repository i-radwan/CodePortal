@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Profile</div>
<div class="container">
 <br> <img src="/images/profile/UserDefault.png" class="img-rounded" alt="Cinque Terre" width="200" height="230"> 
</div>
                    <div class="panel-body">
                       <h2>{{ $userName }}</h2>   
                       <p><i class="fa fa-clock-o"></i> Joined {{ $date }}</p>
                       <button class="btn" :disabled='false'>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection