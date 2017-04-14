{{--Display single contest problems info--}}
<table class="table table-bordered table-hover text-center">
    <thead>
        <tr>
            <th class="text-center">ID</th>
            <th class="text-center">Name</th>
            <th class="text-center">Judge</th>
        </tr>
    </thead>
    <tbody>
        {{--@foreach($problems as $problem)--}}
            {{--@php($verdict = $problem->simpleVerdict($user))--}}

            {{--<tr class="{{ $verdict == Constants::SIMPLE_VERDICT_ACCEPTED ? 'success' : ($verdict == Constants::SIMPLE_VERDICT_WRONG_SUBMISSION ? 'danger' : '') }}">--}}
                {{--ID--}}
                {{--<td>{{ Utilities::generateProblemNumber($problem) }}</td>--}}

                {{--Name--}}
                {{--<td>--}}
                    {{--<a href="{{ Utilities::generateProblemLink($problem) }}" target="_blank">--}}
                        {{--{{ $problem->name }}--}}
                    {{--</a>--}}
                {{--</td>--}}

                {{--Judge--}}
                {{--<td>--}}
                    {{--<a href="{{ Constants::JUDGES[$problem->judge_id][Constants::JUDGE_LINK_KEY] }}" target="_blank">--}}
                        {{--{{ Constants::JUDGES[$problem->judge_id][Constants::JUDGE_NAME_KEY] }}--}}
                    {{--</a>--}}
                {{--</td>--}}
            {{--</tr>--}}
        {{--@endforeach--}}
    </tbody>
</table>
