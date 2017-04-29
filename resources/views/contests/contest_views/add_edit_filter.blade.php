<div class="panel panel-default problems-filters-panel">
    <div class="panel-body">
        {{--Judges checkboxes--}}
        <div>
            Online Judges:
            @foreach ($judges as $judge)
                <div class="checkbox">
                    <label>
                        <input class="judgeState" type="checkbox"
                               {{ in_array($judge->id, $judges->toArray()) ? 'checked' : '' }}
                               value="{{ $judge->id }}"
                               id="judge-checkbox-{{ $judge->id }}"
                               onchange="app.syncDataWithSession(app.judgesSessionKey, '{{ $judge->id }}', true)">
                        {{ $judge->name }}
                    </label>
                </div>
            @endforeach
        </div>
        <hr>
        {{--Tags AutoComplete--}}
        @include('components.auto_complete', ['itemsType' => 'tags', 'itemName' => 'Tag', 'itemsLink' => route(\App\Utilities\Constants::ROUTES_CONTESTS_TAGS_AUTO_COMPLETE), 'hiddenID' => '', 'hiddenName' => ''])

        {{--Apply filters & Clear buttons--}}
        <p>
            <input class="btn btn-default" value="Apply Filters" id="apply-filters"
                   onclick="app.applyFilters('{{ $syncFiltersURL }}', '{{csrf_token()}}', '{{ Request::url() }}')"/>

            <span onclick="app.clearProblemsFilters('{{ $detachFiltersURL }}', '{{csrf_token()}}', '{{ Request::url() }}')"
                  class="btn btn-link text-dark pull-right"
                  id="clear-table-sorting-link">Clear</span>
        </p>
    </div>
</div>
