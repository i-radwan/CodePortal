{{--Display single contest main info--}}
<div class="row contest-info">
    <div class="col-md-6">
        <p class="contest-time pull-right">{{ $contestTime }}</p>
    </div>
    <div class="col-md-6 contest-details">
        <p>
            <strong>Owner: </strong>
            <a href="{{ url('profile/' . $ownerUsername) }}">{{ $ownerUsername }}</a>
        </p>

        @if(count($contestOrganizers) > 0)
            <p>
                <strong>Organizers: </strong>
                @foreach($contestOrganizers as $organizer)
                    <a href="{{ url('profile/' . $organizer )}}">{{ $organizer }}</a>

                    @if(!$loop->last)
                        ,
                    @endif
                @endforeach
            </p>
        @endif

        <p>
            <strong>Duration: </strong> {{ $contestDuration }} hrs
        </p>
    </div>
</div>