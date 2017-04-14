{{--Display single contest ask question section --}}
<div class="contest-ask-question centered">
    <div class="contest-ask-question-title">Ask Question!</div>

    <form method="post" action="{{ url('contest/question/' . $contestID) }}">
        {{csrf_field()}}

        {{--Question title--}}
        <input class="form-control" type="text" name="title" placeholder="Title..." required/>

        {{--Question content--}}
        <textarea class="form-control" cols="30" rows="5" name="content" placeholder="Question content..." required></textarea>

        {{--Display Errors--}}
        @if(Session::has('question-error'))
            <p class="error-msg">{{ Session::get('question-error') }}</p>
        @endif

        <button type="submit" class="btn btn-primary">Ask Question!</button>
    </form>
</div>