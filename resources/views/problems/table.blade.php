<table class="table table-bordered table-hover text-center">
    {{--Headings--}}
    <thead>
    <tr>
        {{--Problem checkboxes--}}
        @if(isset($checkBoxes) && $checkBoxes = 'true')
            <th data-field="state" data-checkbox="true">#</th>
        @endif

        {{--ID--}}
        @include('problems.sortable_heading', [
            'title' => 'ID',
            'width' => '7%',
            'sortParam' => \App\Utilities\Constants::URL_QUERY_SORT_PARAM_ID_KEY
        ])

        {{--Name--}}
        @include('problems.sortable_heading', [
            'title' => 'Name',
            'width' => '46%',
            'sortParam' => \App\Utilities\Constants::URL_QUERY_SORT_PARAM_NAME_KEY
        ])

        {{--# Accepted--}}
        @include('problems.sortable_heading', [
            'title' => '#Acc.',
            'width' => '7%',
            'sortParam' => \App\Utilities\Constants::URL_QUERY_SORT_PARAM_ACCEPTED_COUNT_KEY
        ])

        {{--Judge--}}
        @include('problems.sortable_heading', [
            'title' => 'Judge',
            'width' => '10%',
            'sortParam' => \App\Utilities\Constants::URL_QUERY_SORT_PARAM_JUDGE_KEY
        ])

        {{--Tags--}}
        <th class="text-center" width="30%">Tags</th>
    </tr>
    </thead>

    @php($user = Auth::user())

    {{--Problems--}}
    <tbody>
    @foreach($problems as $problem)
        @php
            $verdict = $problem->simpleVerdict($user);
            $rawID = $problem[\App\Utilities\Constants::FLD_PROBLEMS_ID];
            $id = \App\Utilities\Utilities::generateProblemNumber($problem);
            $name = $problem[\App\Utilities\Constants::FLD_PROBLEMS_NAME];
            $solvedCount = $problem[\App\Utilities\Constants::FLD_PROBLEMS_SOLVED_COUNT];
            $link = \App\Utilities\Utilities::generateProblemLink($problem);
            $judgeData = \App\Utilities\Constants::JUDGES[$problem->judge_id];
            $judgeLink = $judgeData[\App\Utilities\Constants::JUDGE_LINK_KEY];
            $judgeName = $judgeData[\App\Utilities\Constants::JUDGE_NAME_KEY];

            if ($verdict == \App\Utilities\Constants::SIMPLE_VERDICT_ACCEPTED)
                $style = 'success';
            elseif ($verdict == \App\Utilities\Constants::SIMPLE_VERDICT_WRONG_SUBMISSION)
                $style = 'danger';
            else
                $style = '';
        @endphp

        <tr class="{{ $style }}">
            {{--Checkbox--}}
            @if(isset($checkBoxes) && $checkBoxes == 'true')
                <td>
                    <input class="check_state"
                           type="checkbox"
                           id="problem-checkbox-{{ $rawID }}"
                           onclick="app.syncDataWithSession(app.problemsIDsSessionKey, '{{ $rawID }}', true, this)"/>
                </td>
            @endif

            {{--ID--}}
            <td>{{ $id }}</td>

            {{--Name--}}
            <td><a href="{{ $link }}" target="_blank">{{ $name }}</a></td>

            {{--Solved Count--}}
            <td>{{ $solvedCount }}</td>

            {{--Judge--}}
            <td><a href="{{ $judgeLink }}" target="_blank">{{ $judgeName }}</a></td>

            {{--Tags--}}
            <td>
                @foreach($problem->tags()->get() as $tag)
                    @php
                        $tagID = $tag[\App\Utilities\Constants::FLD_TAGS_ID];
                        $tagName = $tag[\App\Utilities\Constants::FLD_TAGS_NAME];
                        $urlQuery = http_build_query([\App\Utilities\Constants::URL_QUERY_TAG_KEY => $tagID]);
                        $url = Request::url() . '?' . $urlQuery;
                    @endphp

                    <a class="problems-table-tag-link" href="{{ $url }}">
                        {{ $tagName }}
                    </a>
                @endforeach
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

{{--Pagination--}}
<div class="text-center">
    {{ $problems->appends(Request::all())->render() }}
</div>

