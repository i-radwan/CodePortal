<table class="table table-bordered">
    {{--Headings--}}
    <thead>
        <tr>
            @include('problems.sortable_heading', ['title' => 'ID', 'sortParam' => Constants::URL_QUERY_SORT_PARAM_ID_KEY])
            @include('problems.sortable_heading', ['title' => 'Name', 'sortParam' => Constants::URL_QUERY_SORT_PARAM_NAME_KEY])
            @include('problems.sortable_heading', ['title' => '#Acc.', 'sortParam' => Constants::URL_QUERY_SORT_PARAM_ACCEPTED_COUNT_KEY])
            @include('problems.sortable_heading', ['title' => 'Judge', 'sortParam' => Constants::URL_QUERY_SORT_PARAM_JUDGE_KEY])

            {{--Tags--}}
            <th class="problems-table-head problems-table-head-tags">Tags</th>
        </tr>
    </thead>

    @php($user = Auth::user())

    {{--Problems--}}
    <tbody>
        {{--TODO: @Samir add checkboxes When adding new contest view--}}
        @foreach($problems as $problem)
            @php($verdict = $problem->simpleVerdict($user))

            <tr class="{{ $verdict == Constants::SIMPLE_VERDICT_ACCEPTED ? 'success' : ($verdict == Constants::SIMPLE_VERDICT_WRONG_SUBMISSION ? 'danger' : '') }}">
                {{--ID--}}
                <td>{{ Utilities::generateProblemNumber($problem) }}</td>

                {{--Name--}}
                <td>
                    <a href="{{ Utilities::generateProblemLink($problem) }}" target="_blank">
                        {{ $problem->name }}
                    </a>
                </td>

                {{--# Accepted--}}
                <td>{{ $problem->solved_count }}</td>

                {{--Judge--}}
                <td>
                    <a href="{{ Constants::JUDGES[$problem->judge_id][Constants::JUDGE_LINK_KEY] }}" target="_blank">
                        {{ Constants::JUDGES[$problem->judge_id][Constants::JUDGE_NAME_KEY] }}
                    </a>
                </td>

                {{--Tags--}}
                <td>
                    @foreach($problem->tags()->get() as $tag)
                        <a class="problems-table-tag-link"
                           href="{{ Request::url() . '?' . http_build_query([Constants::URL_QUERY_TAG_KEY => $tag->id]) }}">
                            {{ $tag->name }}
                        </a>
                    @endforeach
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

{{--Pagination--}}
{{ $problems->appends(Request::all())->render() }}
