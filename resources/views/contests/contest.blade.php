@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <a href="{{url('/contest/add')}}"><span class="pull-right text-dark btn btn-link margin-5px">Delete</span></a>
                    <a href="{{url('/contest/add')}}"><span class="pull-right text-dark btn btn-link margin-5px">Leave</span></a>
                    <div class="panel-heading">
                        Contest
                    </div>

                    <div class="panel-body">
                        List of all contests
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection