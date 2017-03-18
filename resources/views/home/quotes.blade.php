<section class="quotes-section">
    <div class="container js-wp-3">
        <h1 class="text-center">What are people saying about competitive programming?</h1>

        <div id="quotes-carousel" class="carousel slide" data-ride="carousel">
            <!-- Indicators -->
            <ol class="carousel-indicators">
                @foreach($quotes as $quote)
                    <li data-target="#quotes-carousel" data-slide-to="{{ $loop->index }}"{!! $loop->first ? ' class="active"' : '' !!}></li>
                @endforeach
            </ol>

            <!-- Wrapper for slides -->
            <div class="carousel-inner" role="listbox">
                @foreach($quotes as $quote)
                    <div class="item{{ $loop->first ? ' active' : '' }}">
                        <div class="container">
                            <div class="col-md-3">
                                <img src="{{ $quote['img'] }}" alt="{{ $quote['name'] }}" class="img-circle center-block quote-image">
                            </div>
                            <div class="col-md-9">
                                <blockquote>
                                    <i class="fa fa-quote-left"></i>
                                    <p>{{ $quote['description'] }}</p>
                                </blockquote>
                                <h3>{{ $quote['name'] }}</h3>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>