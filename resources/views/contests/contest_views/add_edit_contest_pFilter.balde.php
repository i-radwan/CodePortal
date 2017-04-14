<div class="panel panel-default problems-filters-panel">
    <div class="panel-heading">Filters</div>


    <div class="panel-body">
        <form action="{{ Request::url() }}" method="get" role="form">

            {{--Search Bar--}}
            <div id="custom-search-input">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Problem Name"
                           name="{{ Constants::URL_QUERY_SEARCH_KEY }}"
                           value="{{ Request::get(Constants::URL_QUERY_SEARCH_KEY) }}">

                    <span class="input-group-btn">
                        <button class="btn btn-default btn-lg" type="submit">
                            <i class="glyphicon glyphicon-search"></i>
                        </button>
                    </span>
                </div>
            </div>

            {{--Judges checkboxes--}}
            <div>
                <h4>Online Judges:</h4>
                @foreach ($judges as $judge)
                <div class="checkbox">
                    <label>
                        <input type="checkbox" {{ in_array($judge->id, Request::get(Constants::URL_QUERY_JUDGES_KEY, [])) ? 'checked' : '' }}
                        name="{{ Constants::URL_QUERY_JUDGES_KEY }}[]"
                        value="{{ $judge->id }}">
                        {{ $judge->name }}
                    </label>
                </div>
                @endforeach
            </div>

            {{--ToDo: Samir tags checkboxes but I will change this later isA to autocomplete--}}
            {{--Tags checkboxes--}}
            <div>
                <h4>Tags:</h4>
                {{-- Auto Complete-- }}
                    </label>
                </div>
                @endforeach
            </div>

            <hr/>

            {{--Apply filters & Clear buttons--}}
            <p>
                <input type="submit" class="btn btn-default" value="Apply Filters"/>
                <a href="{{ Request::url() }}" class="btn btn-link text-dark pull-right">Clear</a>
            </p>
        </form>
    </div>
</div>
