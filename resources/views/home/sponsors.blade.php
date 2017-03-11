<div class="container">
    <h2 class="text-center">Our Sponsors</h2>

    <div class="row vertical-padding">
        @foreach($sponsors as $sponsor)
            <a href="{{ $sponsor['url'] }}">
                <div class="col-md-4 text-center">
                    <img src="{{ $sponsor['img'] }}" alt="{{ $sponsor['name'] }}" class="sponsor-img">
                    <h3>{{ $sponsor['name'] }}</h3>
                </div>
            </a>
        @endforeach
    </div>
</div>