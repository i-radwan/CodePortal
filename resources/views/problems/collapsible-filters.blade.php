<div class="panel panel-default problems-filters-panel">
    <div class="panel-heading">
        Filters
    </div>

    <div id="filters" class="panel-collapse">
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

                <div class="hidden-filters" id="hidden-filters">
                    {{--Judges checkboxes--}}
                    <div>
                        <h4>Online Judges:</h4>
                        @foreach ($judges as $judge)
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox"
                                           {{ in_array($judge[\App\Utilities\Constants::FLD_JUDGES_ID], Request::get(\App\Utilities\Constants::URL_QUERY_JUDGES_KEY, [])) ? 'checked' : '' }}
                                           name="{{ \App\Utilities\Constants::URL_QUERY_JUDGES_KEY }}[]"
                                           value="{{ $judge[\App\Utilities\Constants::FLD_JUDGES_ID] }}">
                                    {{ $judge[\App\Utilities\Constants::FLD_JUDGES_NAME] }}
                                </label>
                            </div>
                        @endforeach
                    </div>

                    {{--Tags auto-complete search bar--}}
                    @include('components.auto_complete', ['itemsType' => 'tags', 'itemName' => 'Tag', 'itemsLink' => url('tags_auto_complete'), 'hiddenID' => \App\Utilities\Constants::URL_QUERY_TAGS_KEY, 'hiddenName' => \App\Utilities\Constants::URL_QUERY_TAG_KEY])

                    <hr/>
                    {{--Apply filters & Clear buttons--}}
                    <p>
                        <input onclick="app.moveProblemsFiltersSessionDataToHiddenFields()"
                               type="submit" class="btn btn-default" value="Apply Filters"/>
                        <a href="{{ Request::url() }}" class="btn btn-link text-dark pull-right">Clear</a>
                    </p>
                </div>
                <div class="text-center">
                    <span class="btn btn-sm btn-link more-filters-button" id="more-filters-button"
                          onclick="$('#hidden-filters').slideToggle();$(this).html(($(this).html() == 'more')?'less':'more')">more</span>
                </div>
            </form>
        </div>
    </div>
</div>
