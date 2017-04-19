<div class="panel panel-default problems-filters-panel">
    <div class="panel-heading">
        <a class="text-dark" data-toggle="collapse" href="#filters">
            Filters
        </a>
    </div>

    <div id="filters" class="panel-collapse collapse in">
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
                                <input type="checkbox"
                                       {{ in_array($judge->id, Request::get(Constants::URL_QUERY_JUDGES_KEY, [])) ? 'checked' : '' }}
                                       name="{{ Constants::URL_QUERY_JUDGES_KEY }}[]"
                                       value="{{ $judge->id }}">
                                {{ $judge->name }}
                            </label>
                        </div>
                    @endforeach
                </div>

                {{--Tags checkboxes--}}
                <div class="container" style="padding: 0">
                    <h4>Tags:</h4>
                    <input type="hidden" id="{{\App\Utilities\Constants::URL_QUERY_TAGS_KEY}}"
                           name="{{ \App\Utilities\Constants::URL_QUERY_TAG_KEY }}">
                    <div class="search-wrapper input-group">
                        <input id="tagsAuto" type="text" class="tagsAuto search-box" placeholder="Enter Tag"
                               autocomplete="off"
                               onkeypress="return event.keyCode != 13;"
                               data-tags-path="{{url('tags_auto_complete')}}"/>
                    </div>
                    <div class="container">
                        <ul id="tagsList" class="tags-list">

                        </ul>
                    </div>
                </div>
                <hr/>

                {{--Apply filters & Clear buttons--}}
                <p>
                    <input onclick="app.moveProblemsFiltersSessionDataToHiddenFields()"
                           type="submit" class="btn btn-default" value="Apply Filters"/>
                    <a href="{{ Request::url() }}" class="btn btn-link text-dark pull-right">Clear</a>
                </p>
            </form>
        </div>
    </div>
</div>
