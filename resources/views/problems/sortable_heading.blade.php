<th class="problems-table-head">
    <a class="problems-table-head-link"
       href="{{ Utilities::getSortURL($sortParam) }}">
        {{ $title }}
        @if(Request::get(Constants::URL_QUERY_SORT_PARAM_KEY) == $sortParam)
            <i class="pull-right fa {{ Request::get(Constants::URL_QUERY_SORT_ORDER_KEY, 'asc') == 'asc' ? 'fa-sort-asc problems-table-sorting-arrow-desc' : 'fa-sort-asc problems-table-sorting-arrow-desc' }}" aria-hidden="true"></i>
        @endif
    </a>
</th>