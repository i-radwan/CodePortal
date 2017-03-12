<section class="quotes-section vertical-padding">
    <div class="container">
        <h2 class="text-center">What people are saying about competitive programming</h2>

        <div id="quotes-carousel" class="carousel slide" data-ride="carousel">
            <!-- Indicators -->
            <ol class="carousel-indicators">
                @foreach($quotes as $quote)
                    <li data-target="#quotes-carousel" data-slide-to="{{ $loop->index }}" class="{{ $loop->first ? 'active' : '' }}"></li>
                @endforeach
            </ol>

            <!-- Wrapper for slides -->
            <div class="carousel-inner" role="listbox">
                @foreach($quotes as $quote)
                    <div class="item text-center {{ $loop->first ? 'active' : '' }}">
                        <img src="{{ $quote['img'] }}" alt="{{ $quote['name'] }}" class="img-circle center-block">
                        <h3>{{ $quote['name'] }}</h3>
                        <p>{{ $quote['description'] }}</p>
                    </div>
                @endforeach
            </div>

            <!-- Controls -->
            <a class="left carousel-control" href="#quotes-carousel" role="button" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="right carousel-control" href="#quotes-carousel" role="button" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    </div>
</section>