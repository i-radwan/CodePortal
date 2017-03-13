<section class="sponsors-section">
    <div class="container">
        <h2 class="text-center">Sponsors</h2>

        <div class="row">
            @foreach($sponsors as $sponsor)
                <div class="col-sm-4 col-xs-6 text-center">
                    <a href="{{ $sponsor['url'] }}" target="_blank">
                        <img src="{{ $sponsor['img'] }}" alt="{{ $sponsor['name'] }}" class="img-circle">
                    </a>
                    <h3>{{ $sponsor['name'] }}</h3>
                </div>
            @endforeach
        </div>
    </div>
</section>