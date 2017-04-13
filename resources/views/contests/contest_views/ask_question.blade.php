{{--Display single contest ask question section --}}
<div class="centered">
    <div class="row">
        <div class="contest-ask-question col-md-12 form-group">
            <div><span class="contest-ask-question-title">Ask Question!</span></div>
            <form class="form-horizontal"
                  action="{{url('contest/question/'.$contestID)}}"
                  method="post">
                {{csrf_field()}}
                <input type="text" name="title" placeholder="Title..." class="form-control"
                       required/>
                <textarea name="content" cols="30" rows="5" class="form-control"
                          placeholder="Question content..." required></textarea>

                {{--Display Errors--}}
                @if(Session::has('question-error'))
                    <p class="error-msg">{{ Session::get('question-error') }}</p>
                @endif
                <input type="submit" value="Ask Question!" class="btn btn-primary"/>
            </form>
        </div>
    </div>
</div>