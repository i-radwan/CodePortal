<table class="table table-bordered table-hover">
    {{--Headings--}}
    <thead>
    <tr>
        @if(isset($checkBoxes) && $checkBoxes = 'true')
            <th data-field="state" data-checkbox="true"></th>
        @endif
        @include('problems.sortable_heading', ['title' => 'ID', 'width' => '7%', 'sortParam' => Constants::URL_QUERY_SORT_PARAM_ID_KEY])
        @include('problems.sortable_heading', ['title' => 'Name', 'width' => '46%', 'sortParam' => Constants::URL_QUERY_SORT_PARAM_NAME_KEY])
        @include('problems.sortable_heading', ['title' => '#Acc.', 'width' => '7%', 'sortParam' => Constants::URL_QUERY_SORT_PARAM_ACCEPTED_COUNT_KEY])
        @include('problems.sortable_heading', ['title' => 'Judge', 'width' => '10%', 'sortParam' => Constants::URL_QUERY_SORT_PARAM_JUDGE_KEY])

        {{--Tags--}}
        <th class="text-center" width="30%">Tags</th>
    </tr>
    </thead>

    @php($user = Auth::user())

    {{--Problems--}}
    <tbody>
    {{--TODO: @Samir add checkboxes When adding new contest view--}}
    @foreach($problems as $problem)
        @php
            $verdict = $problem->simpleVerdict($user);
            $id = \App\Utilities\Utilities::generateProblemNumber($problem);
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
            @if(isset($checkBoxes) && $checkBoxes = 'true')
                <td>
                    <input class="checkState"
                           type="checkbox"
                           value="{{ ($problem->id) }}"
                           onclick="syncProblemState()"
                            {{ isset($checkedRows) ? (in_array($problem->id, $checkedRows) ? 'checked' : '') : ''}}>
                </td>
            @endif

            {{--ID--}}
            <td>{{ $id }}</td>

            {{--Name--}}
            <td><a href="{{ $link }}" target="_blank">{{ $problem->name }}</a></td>

            {{--Solved Count--}}
            <td>{{ $problem->solved_count }}</td>

            {{--Judge--}}
            <td><a href="{{ $judgeLink }}" target="_blank">{{ $judgeName }}</a></td>

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

<script type="text/javascript">
    function syncProblemState() {
        //get the check boxes in each page
        var checkedStates = [];
        var checkedRows = [];
        var j = 0;
        var checkboxes = document.getElementsByClassName('checkState');
        for (var i = 0; checkboxes[i]; ++i) {
            checkedRows[j] = checkboxes[i].value;
            checkedStates[j] = (checkboxes[i].checked == true) ? 1 : 0;
            j = j + 1;
        }
        $.ajax({
            url: "{{Request::url()}}/checkRowsSync",
            type: 'POST',
            data: {
                _token: "{{csrf_token()}}",
                checkedRows: checkedRows,
                checkedStates: checkedStates
            },
            success: function (data) {
                console.log(data);
            }
        });
    }
</script>
