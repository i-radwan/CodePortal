<div class="container">
    @foreach($features as $feature)
        <div class="row vertical-padding">
            <div class="col-md-8 {{ $loop->index % 2 == 1 ? 'col-md-push-4' : '' }}">
                <h2>{{ $feature['title'] }}</h2>
                <p>{{ $feature['description'] }}</p>
                <a href="#" class="btn btn-primary btn-lg">{{ $feature['title'] }}</a>
            </div>

            <div class="col-md-4 {{ $loop->index % 2 == 1 ? 'col-md-pull-8' : '' }}">
                <img src="{{ $feature['img'] }}" alt="{{ $feature['title'] }}" class="img-rounded center-block img-limit-size">
            </div>
        </div>
        <hr>
    @endforeach
</div>