{{--Display single contest problems info--}}
<table class="table table-bordered table-hover text-center">
    <thead>
    <tr>
        <th class="text-center problems-reorder-view">#</th>
        <th class="text-center">ID</th>
        <th class="text-center">Name</th>
        <th class="text-center">Solved</th>
        <th class="text-center">Judge</th>
    </tr>
    </thead>

    @php($user = Auth::user())

    <tbody id="contest-problems-tbody">
    @foreach($problems as $problem)
        @php
            $problem = new \App\Models\Problem((array)$problem);
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
            {{--Reorder view--}}
            <td class="problems-reorder-view index" data-problem-id="{{$problem->id}}"><i class="fa fa-bars"
                                                                                          aria-hidden="true"></i></td>

            {{--ID--}}
            <td>{{ $id }}</td>

            {{--Name--}}
            <td><a href="{{ $link }}" target="_blank">{{ $problem->name }}</a></td>

            {{--Solved Count--}}
            <td>{{ $problem->solved_count }}</td>

            {{--Judge--}}
            <td><a href="{{ $judgeLink }}" target="_blank">{{ $judgeName }}</a></td>
        </tr>
    @endforeach
    </tbody>
</table>
