<div class="container">
    @foreach($features as $feature)
        <div class="row feature-item">
            <div class="col-md-6{{ $loop->index % 2 == 1 ? ' col-md-push-6' : '' }}">
                <h1>{{ $feature['title'] }}</h1>

                <div class="hidden-xs hidden-sm">
                    <p class="text-justify">{{ $feature['description'] }}</p>
                    <a href="{{ $feature['url'] }}"
                       class="btn btn-default btn-lg{{ $loop->index % 2 == 1 ? ' pull-right' : '' }}">
                        {{ $feature['link_title'] }}
                    </a>
                </div>
            </div>

            <div class="col-md-6{{ $loop->index % 2 == 1 ? ' col-md-pull-6' : '' }}">
                <img src="{{ $feature['img'] }}" alt="{{ $feature['title'] }}" class="img-responsive img-rounded center-block">
            </div>

            <div class="col-xs-12 visible-xs visible-sm text-center">
                <p class="text-justify">{{ $feature['description'] }}</p>
                <a href="{{ $feature['url'] }}" class="btn btn-default btn-lg">{{ $feature['link_title'] }}</a>
            </div>
        </div>

        @if(!$loop->last)
            <hr>
        @endif
    @endforeach
</div>