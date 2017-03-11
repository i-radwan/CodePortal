@extends('layouts.app')

@section('content')
    <div class="jumbotron flex-center">
        <div class="container text-center">
            <h1>Code Portal</h1>
            <h3>Practice Competitive Programming</h3>

            {{-- Search form --}}
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <form class="jumbotron-search-form" role="form" method="POST" action="">
                        {{ csrf_field() }}

                        <div class="input-group input-group-lg">
                            <input type="text" class="form-control" id="search" placeholder="Search for..." required>
                            <span class="input-group-btn">

                                <button type="button" class="btn btn-primary">
                                    <span class="glyphicon glyphicon-search"></span>
                                    Search
                                </button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Main features--}}
    <div class="container">
        <div class="row vertical-padding">
            <div class="col-md-8">
                <h2>Prepare Contests</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
            </div>

            <div class="col-md-4">
                <img src="images/wolf.jpg" alt="contest" class="img-rounded center-block img-limit-size">
            </div>
        </div>

        <hr>

        <div class="row vertical-padding">
            <div class="col-md-8 col-md-push-4">
                <h2>Solve Problems</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
            </div>

            <div class="col-md-4 col-md-pull-8">
                <img src="images/sloth.jpg" alt="contest" class="img-rounded center-block img-limit-size">
            </div>
        </div>

        <hr>

        <div class="row vertical-padding">
            <div class="col-md-8">
                <h2>Write Blogs</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
            </div>

            <div class="col-md-4">
                <img src="images/wolf.jpg" alt="contest" class="img-rounded center-block img-limit-size">
            </div>
        </div>

        <hr>

        <div class="row feature-item">
            <div class="col-md-8 col-md-push-4">
                <h2>Manage Groups</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
            </div>

            <div class="col-md-4 col-md-pull-8">
                <img src="images/sloth.jpg" alt="contest" class="img-rounded center-block img-limit-size">
            </div>
        </div>

        <hr>
    </div>

    {{-- Quotes about problem solving --}}
    <section class="quotes-section vertical-padding">
        <div class="container">
            <h2 class="text-center">What are people saying about about competitive programming</h2>

            <div id="quotes-carousel" class="carousel slide" data-ride="carousel">
                <!-- Indicators -->
                <ol class="carousel-indicators">
                    <li data-target="#quotes-carousel" data-slide-to="0" class="active"></li>
                    <li data-target="#quotes-carousel" data-slide-to="1"></li>
                    <li data-target="#quotes-carousel" data-slide-to="2"></li>
                    <li data-target="#quotes-carousel" data-slide-to="3"></li>
                </ol>

                <!-- Wrapper for slides -->
                <div class="carousel-inner" role="listbox">
                    <div class="item text-center active">
                        <img src="images/omar.jpg" alt="Person" class="img-circle center-block">
                        <h3>Omar Wael</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                    </div>

                    <div class="item text-center">
                        <img src="images/omar-darwish.jpg" alt="Person" class="img-circle center-block">
                        <h3>Mostafa Darwish</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                    </div>

                    <div class="item text-center">
                        <img src="images/omar.jpg" alt="Person" class="img-circle center-block">
                        <h3>Omar Wael</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                    </div>

                    <div class="item text-center">
                        <img src="images/omar-darwish.jpg" alt="Person" class="img-circle center-block">
                        <h3>Mostafa Darwish</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                    </div>
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

    {{-- Sponsors --}}
    <div class="container">
        <h2 class="text-center">Our Sponsors</h2>

        <div class="row vertical-padding">
            <div class="col-md-4 text-center">
                <img src="images/codeforces.png" alt="Codeforces" class="sponsor-img">
                <h3>Codeforces</h3>
            </div>

            <div class="col-md-4 text-center">
                <img src="images/uva.png" alt="UVA" class="sponsor-img">
                <h3>UVA Online Judge</h3>
            </div>

            <div class="col-md-4 text-center">
                <img src="images/live-archive.png" alt="Live Archive" class="sponsor-img">
                <h3>Live Archive</h3>
            </div>
        </div>
    </div>
@endsection