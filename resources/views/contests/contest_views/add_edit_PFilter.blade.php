<div class="panel panel-default problems-filters-panel">
    <div class="panel-body">
        <form action="{{ Request::url() }}" method="get" role="form">
            {{--Judges checkboxes--}}
            <div>
                <h5>Online Judges:</h5>
                @foreach ($judges as $judge)
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" {{ in_array($judge->id, Request::get(Constants::URL_QUERY_JUDGES_KEY, [])) ? 'checked' : '' }}
                            name="{{ Constants::URL_QUERY_JUDGES_KEY }}[]"
                                   value="{{ $judge->id }}">
                            {{ $judge->name }}
                        </label>
                    </div>
                @endforeach
            </div>
            <hr>
            <div>
                <h5>Tags:</h5>
                <div class="search-wrapper" >
                        <input type="text" name="focus" required class="search-box" placeholder="Enter search term" />
                        <button class="close-icon" type="reset"></button>
                </div>
            </div>
        </form>
    </div>
</div>
