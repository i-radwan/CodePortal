@php
    if (Request::get(\App\Utilities\Constants::URL_QUERY_SORT_ORDER_KEY, 'asc') == 'asc')
        $style = 'fa-sort-asc problems-table-sorting-arrow-asc';
    else
        $style = 'fa-sort-desc problems-table-sorting-arrow-desc';
@endphp

<th class="text-center problems-table-head-sortable"
    width="{{ $width }}">

    <a href="{{ \App\Utilities\Utilities::getSortURL($sortParam) }}">
        {{ $title }}

        @if(Request::get(\App\Utilities\Constants::URL_QUERY_SORT_PARAM_KEY) == $sortParam)
            <i class="pull-right fa {{ $style }}" aria-hidden="true"></i>
        @endif
    </a>
</th>