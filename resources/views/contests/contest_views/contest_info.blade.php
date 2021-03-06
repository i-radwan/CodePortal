{{--Display single contest main info--}}
<div class="container">
    <div class="row contest-info">
        <div class="col-md-7 col-sm-5 col-xs-12 contest-info-time">
            <a target="_blank"
               href="https://www.timeanddate.com/worldclock/fixedtime.html?day={{ $contestDay }}&month={{ $contestMonth }}&year={{ $contestYear }}&hour={{ $contestHour }}&min={{ $contestMinute }}">
                <p class="contest-time pull-right">{{ $contestTime }}</p>
            </a>
        </div>

        <div class="col-md-5 col-sm-7 cols-xs-12 contest-details">
            <p class="owner-p">
                <strong>Owner:</strong>

                <a href="{{ route(\App\Utilities\Constants::ROUTES_PROFILE, $ownerUsername) }}">
                    {{ $ownerUsername }}
                </a>
            </p>

            @if(count($contestOrganizers))
                <p class="organizers-p">
                    <strong>Organizers:</strong>

                    @foreach($contestOrganizers as $organizer)
                        <a href="{{ route(\App\Utilities\Constants::ROUTES_PROFILE, $organizer) }}">
                            {{ $organizer }}
                        </a>

                        @if(!$loop->last)
                            ,
                        @endif
                    @endforeach
                </p>
            @endif

            <p class="duration-p"><strong>Duration: </strong>{{ $contestDuration }}</p>
        </div>
    </div>
</div>