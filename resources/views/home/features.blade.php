<div class="container">
    @foreach($features as $feature)
        <div class="row feature-item">
            <div class="col-md-6{{ $loop->index % 2 == 1 ? ' col-md-push-6' : '' }} feature-content">
                <h2>{{ $feature['title'] }}</h2>
                <p>{{ $feature['description'] }}</p>
                <a href="{{ $feature['url'] }}" class="btn btn-primary btn-lg">{{ $feature['link_title'] }}</a>
            </div>

            <div class="col-md-6{{ $loop->index % 2 == 1 ? ' col-md-pull-6' : '' }}">
                <img src="{{ $feature['img'] }}" alt="{{ $feature['title'] }}" class="img-responsive img-rounded center-block">
            </div>
        </div>

        @if(!$loop->last)
            <hr>
        @endif
    @endforeach
</div>