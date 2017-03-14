<div class="jumbotron flex-center">
    <div class="container text-center">
        <h1><strong>{{ config('app.name') }}</strong></h1>
        <h3>Practise Competitive Programming</h3>

        {{-- Search form --}}
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <form role="form" method="POST" action="">
                    {{ csrf_field() }}

                    <div class="input-group input-group-lg">
                        <input type="text" class="form-control" id="search" placeholder="Search for..." required>

                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-primary">
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