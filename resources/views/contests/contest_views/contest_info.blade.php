{{--Display single contest main info--}}
<div class="container">
    <div class="row contest-info">
        <div class="col-md-7 col-sm-5 col-xs-12 contest-info-time">
            <p class="contest-time pull-right">{{ $contestTime }}</p>
        </div>
        <div class="col-md-5 col-sm-7 cols-xs-12 contest-details">
            <p>
                <strong>Owner:</strong>
                <a href="{{ url('profile/'.$ownerUsername) }}">{{ $ownerUsername }}</a>
            </p>
            @if(count($contestOrganizers))
                <p>
                    <strong>Organizers:</strong>
                    @foreach($contestOrganizers as $organizer)
                        <a href="{{url('profile/'.$organizer)}}">{{$organizer}}</a>

                        @if(!$loop->last)
                            ,
                        @endif
                    @endforeach
                </p>
            @endif
            <p><strong>Duration: </strong>{{ $contestDuration }} hrs</p>
        </div>
    </div>
</div>