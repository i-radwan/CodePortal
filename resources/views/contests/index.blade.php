@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default contests-panel">
                    <a href="{{url('/contest/add')}}"><span
                                class="pull-right text-dark btn btn-link margin-5px">New</span></a>
                    <div class="panel-heading contests-panel-head">
                        Contests
                    </div>
                    <div class="panel-body contests-panel-body">
                        @if(count($data) > 0)
                            <div class="container contests-table-container">
                                <table class="table table-bordered" id="contests_table">
                                    <thead>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th class="text-center" width="30%">Name</th>
                                        <th class="text-center">Time</th>
                                        <th class="text-center">Duration</th>
                                        <th class="text-center">Owner</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($data[Constants::CONTESTS_CONTESTS_KEY] as $contest)
                                        <tr>
                                            <td>{{$contest->id}}</td>
                                            <td><a href="{{url('contest/'.$contest->id)}}">{{$contest->name}}</a></td>
                                            <td>{{$contest->time}}</td>
                                            <td>{{$contest->duration}}</td>
                                            <td>
                                                <a href="{{url('profile/'.$contest->owner->username)}}">{{$contest->owner->username}}</a>
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                                {{--Pagination--}}
                                <nav aria-label="Page navigation example">
                                    <ul class="pagination" max-size='12'>
                                        <li class="page-item {{isset($data[Constants::CONTESTS_PAGINATOR_KEY][Constants::PAGINATOR_PREV_URL])? "":"disabled"}}">
                                            <a class="page-link"
                                               href="{{isset($data[Constants::CONTESTS_PAGINATOR_KEY][Constants::PAGINATOR_PREV_URL]) ? $data[Constants::CONTESTS_PAGINATOR_KEY][Constants::PAGINATOR_PREV_URL] : ""}}"
                                               aria-label="Previous">
                                                <span aria-hidden="true">&laquo;</span>
                                                <span class="sr-only">Previous</span>
                                            </a>
                                        </li>
                                        @for ($i = $data[Constants::CONTESTS_PAGINATOR_KEY][Constants::PAGINATOR_START_LIMIT]; ($i <= $data[Constants::CONTESTS_PAGINATOR_KEY][Constants::PAGINATOR_END_LIMIT]) ; $i++)
                                            <li class={{($data[Constants::CONTESTS_PAGINATOR_KEY][Constants::PAGINATOR_CURRENT_PAGE] == $i ? "active" : "")}}>
                                                <a class="page-link"
                                                   href={{Utilities::getURL("page", $i, url()->current(), Request::fullUrl(), false)}}>{{$i}}</a>
                                            </li>
                                        @endfor
                                        <li class="page-item {{(isset($data[Constants::CONTESTS_PAGINATOR_KEY][Constants::PAGINATOR_NEXT_URL]) ? ("") : ("disabled"))}}">
                                            <a class="page-link"
                                               href="{{(isset($data[Constants::CONTESTS_PAGINATOR_KEY][Constants::PAGINATOR_NEXT_URL]) ? $data[Constants::CONTESTS_PAGINATOR_KEY][Constants::PAGINATOR_NEXT_URL] : "")}}"
                                               aria-label="Next">
                                                <span aria-hidden="true">&raquo;</span>
                                                <span class="sr-only">Next</span>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        @else
                            <p class="no-contests-msg">No contests!</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection