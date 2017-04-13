<div class="container problems-table-container">
    <table class="table table-bordered" id="problems_table">
        {{--Headings--}}
        <thead>
            <tr>
                {{--ID--}}
                <th class="problems-table-head">
                    <a class="problems-table-head-link"
                       href="{{ Utilities::getSortURL('id') }}">
                        ID
                        @if(Request::get('sort') == 'id')
                            <i class="pull-right fa {{ Request::get('order', 'asc') == 'desc' ? 'fa-sort-desc problems-table-sorting-arrow-desc' : 'fa-sort-asc problems-table-sorting-arrow-asc' }}" aria-hidden="true"></i>
                        @endif
                    </a>
                </th>
                {{--Name--}}
                <th class="problems-table-head">
                    <a class="problems-table-head-link"
                       href="{{ Utilities::getSortURL('name') }}">
                        Name
                        @if(Request::get('sort') == 'name')
                            <i class="pull-right fa {{ Request::get('order', 'asc') == 'desc' ? 'fa-sort-desc problems-table-sorting-arrow-desc' : 'fa-sort-asc problems-table-sorting-arrow-asc' }}" aria-hidden="true"></i>
                        @endif
                    </a>
                </th>
                {{--# Accepted--}}
                <th class="problems-table-head">
                    <a class="problems-table-head-link"
                       href="{{ Utilities::getSortURL('acceptedCount') }}">
                        #Acc.
                        @if(Request::get('sort') == 'acceptedCount')
                            <i class="pull-right fa {{ Request::get('order', 'asc') == 'desc' ? 'fa-sort-desc problems-table-sorting-arrow-desc' : 'fa-sort-asc problems-table-sorting-arrow-asc' }}" aria-hidden="true"></i>
                        @endif
                    </a>
                </th>
                {{--Judge--}}
                <th class="problems-table-head">
                    <a class="problems-table-head-link"
                       href="{{ Utilities::getSortURL('judge') }}">
                        Judge
                        @if(Request::get('sort') == 'judge')
                            <i class="pull-right fa {{ Request::get('order', 'asc') == 'desc' ? 'fa-sort-desc problems-table-sorting-arrow-desc' : 'fa-sort-asc problems-table-sorting-arrow-asc' }}" aria-hidden="true"></i>
                        @endif
                    </a>
                </th>
                {{--Tags--}}
                <th class="problems-table-tags-head">
                    <span class="table-head">Tags</span>
                </th>
            </tr>
        </thead>

        {{--Problems--}}
        <tbody>
            {{--TODO (Samir) Adding CheckBoxes When Adding New Contest View--}}
            @foreach ($problems as $problem)
                {{--TODO: enhance the below line--}}
                <tr class="{{ $problem->simpleVerdict(Auth::user()) ==  Constants::TABLE_ROW_STATE_SUCCESS ? 'bg-success' : $problem->simpleVerdict(Auth::user()) == Constants::TABLE_ROW_STATE_DANGER ? 'bg-warning' : ''}}">
                    {{--ID--}}
                    <td>
                        {{ Utilities::generateProblemNumber($problem) }}
                    </td>

                    {{--Name--}}
                    <td>
                        <a href="{{ Utilities::generateProblemLink($problem) }}" target="_blank">
                            {{ $problem->name }}
                        </a>
                    </td>

                    {{--# Accepted--}}
                    <td>
                        {{ $problem->solved_count }}
                    </td>

                    {{--Judge--}}
                    <td>
                        <a href="{{ Constants::JUDGES[$problem->judge_id][Constants::JUDGE_LINK_KEY] }}" target="_blank">
                            {{ Constants::JUDGES[$problem->judge_id][Constants::JUDGE_NAME_KEY] }}
                        </a>
                    </td>

                    {{--Tags--}}
                    <td>
                        @foreach($problem->tags()->get() as $tag)
                            <a class="problems-table-tags-links"
                               href="{{ Request::url() . '?' . http_build_query(['tag' => $tag->id]) }}">
                                {{ $tag->name . ($loop->remaining >= 1 ?  ', ' : '') }}
                            </a>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{--Pagination--}}
    {{ $problems->appends(Request::all())->render() }}
</div>
