<section class="quotes-section">
    <div class="container">
        <h1 class="text-center">What people are saying about competitive programming</h1>

        <div id="quotes-carousel" class="carousel slide" data-ride="carousel">
            <!-- Indicators -->
            <ol class="carousel-indicators">
                @foreach($quotes as $quote)
                    <li data-target="#quotes-carousel" data-slide-to="{{ $loop->index }}" {!! $loop->first ? 'class="active"' : '' !!}></li>
                @endforeach
            </ol>

            <!-- Wrapper for slides -->
            <div class="carousel-inner" role="listbox">
                @foreach($quotes as $quote)
                    <div class="item text-center{{ $loop->first ? ' active' : '' }}">
                        <img src="{{ $quote['img'] }}" alt="{{ $quote['name'] }}" class="img-circle center-block">
                        <h3>{{ $quote['name'] }}</h3>
                        <p>{{ $quote['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>