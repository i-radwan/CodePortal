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

            {{--Tags checkboxes--}}
            {{--<div class="container" style="padding: 0">--}}
                {{--<h4>Tags:</h4>--}}
                {{--<div class="row">--}}
                    {{--@foreach ($tags as $tag)--}}
                        {{--<div class="col-sm-3 checkbox" style="margin-top: 0">--}}
                            {{--<label>--}}
                                {{--<input type="checkbox" {{ in_array($tag->id, Request::get(Constants::URL_QUERY_TAGS_KEY, [])) ? 'checked' : '' }}--}}
                                       {{--name="{{ Constants::URL_QUERY_TAGS_KEY }}[]"--}}
                                       {{--value="{{ $tag->id }}">--}}
                                {{--{{ $tag->name }}--}}
                            {{--</label>--}}
                        {{--</div>--}}
                    {{--@endforeach--}}
                {{--</div>--}}
            {{--</div>--}}

            {{--ToDo: Samir tags checkboxes but I will change this later isA to autocomplete--}}
            {{--Tags checkboxes--}}
            <div>
                <h4>Tags:</h4>
                @foreach ($tags as $tag)
                    <div class="checkbox problems-filters-tags-checkboxes">
                        <label>
                            <input type="checkbox" {{ in_array($tag->id, Request::get(Constants::URL_QUERY_TAGS_KEY, [])) ? 'checked' : '' }}
                                   name="{{ Constants::URL_QUERY_TAGS_KEY }}[]"
                                   value="{{ $tag->id }}">
                            {{ $tag->name }}
                        </label>
                    </div>
                @endforeach
            </div>

            <hr/>

            {{--Apply filters & Clear buttons--}}
            <p>
                <input class="btn btn-default pull-right" type="submit" value="Apply Filters"/>
                <a href="{{ Request::url() }}" class="btn btn-link text-dark">Clear</a>
            </p>
        </form>
    </div>
</div>
