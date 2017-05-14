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
                               name="{{ \App\Utilities\Constants::URL_QUERY_SEARCH_KEY }}"
                               value="{{ Request::get(\App\Utilities\Constants::URL_QUERY_SEARCH_KEY) }}">

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
                            @php
                                $judgeID = $judge[\App\Utilities\Constants::FLD_JUDGES_ID];
                                $judgeName = $judge[\App\Utilities\Constants::FLD_JUDGES_NAME];
                                $requestedJudges = Request::get(\App\Utilities\Constants::URL_QUERY_JUDGES_KEY, []);
                            @endphp

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox"
                                           {{ in_array($judgeID, $requestedJudges) ? 'checked' : '' }}
                                           name="{{ \App\Utilities\Constants::URL_QUERY_JUDGES_KEY }}[]"
                                           value="{{ $judgeID }}">
                                    {{ $judgeName }}
                                </label>
                            </div>
                        @endforeach
                    </div>

                    {{--Tags auto-complete search bar--}}
                    @include('components.auto_complete', [
                        'itemsType' => 'tags',
                        'itemName' => 'Tag',
                        'itemsLink' => route(\App\Utilities\Constants::ROUTES_CONTESTS_TAGS_AUTO_COMPLETE),
                        'hiddenID' => \App\Utilities\Constants::URL_QUERY_TAGS_KEY,
                        'hiddenName' => \App\Utilities\Constants::URL_QUERY_TAG_KEY
                    ])

                    <hr/>

                    {{--Apply filters & Clear buttons--}}
                    <p>
                        <input type="submit"
                               class="btn btn-default"
                               value="Apply Filters"
                               onclick="app.moveProblemsFiltersSessionDataToHiddenFields()"/>

                        <a href="{{ Request::url() }}" class="btn btn-link text-dark pull-right">Clear</a>
                    </p>
                </div>

                <div class="text-center">
                    <span class="btn btn-sm btn-link more-filters-button"
                          id="more-filters-button"
                          onclick="$('#hidden-filters').slideToggle();$(this).html(($(this).html() == 'more')?'less':'more')">
                        more
                    </span>
                </div>
            </form>
        </div>
    </div>
</div>
