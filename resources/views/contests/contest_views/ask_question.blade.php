{{--Display single contest ask question section --}}
<div class="contest-ask-question centered">
    <div class="contest-ask-question-title">Ask Question!</div>

    <form method="post" action="{{ route(\App\Utilities\Constants::ROUTES_CONTESTS_QUESTIONS_STORE, $contestID) }}">
        {{ csrf_field() }}

        {{--Question title--}}
        <input class="form-control" type="text" id="title" name="title" placeholder="Title..." required/>

        {{--Question problem--}}
        <select class="form-control" name="problem_id" id="problem_id" required>
            @foreach($problems as $problem)
                @php
                    $problem = (array)$problem;
                    $problemId = $problem[\App\Utilities\Constants::FLD_PROBLEMS_ID];
                    $problemName = $problem[\App\Utilities\Constants::FLD_PROBLEMS_NAME];
                @endphp

                <option value="{{ $problemId }}">{{ $problemName }}</option>
            @endforeach
        </select>

        {{--Question content--}}
        <textarea class="form-control" cols="30" rows="5" name="content" id="content" placeholder="Question content..."
                  required></textarea>

        {{--Display Errors--}}
        @if(Session::has('question-error'))
            <p class="error-msg">{{ Session::get('question-error') }}</p>
        @endif

        <button type="submit" id="testing-ask-question-btn" class="btn btn-primary">Ask Question!</button>
    </form>
</div>