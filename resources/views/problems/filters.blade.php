<div class="container filters-table-container">
    <div class="row">
        <div class="col-md-12">
            <form action="{{ Request::url() }}" method="get">

                {{--Search Bar--}}
                <div id="custom-search-input">
                    <div class="col-md-12 input-group">
                        <input type="text" class="form-control" name="q" placeholder="Problem Name" value="{{ Request::get('q', '') }}">
                        <span class="input-group-btn">
                            <button class="btn btn-info btn-lg" type="submit">
                                <i class="glyphicon glyphicon-search"></i>
                            </button>
                        </span>
                    </div>
                </div>

                {{--Judges checkboxes--}}
                <div>
                    <h4>Online Judges:</h4>
                    @foreach ($judges as $judge)
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" {{ in_array($judge->id, Request::get('judges', [])) ? 'checked' : '' }}
                                       name="judges[]"
                                       value="{{ $judge->id }}">
                                {{ $judge->name }}
                            </label>
                        </div>
                    @endforeach
                </div>

                {{--ToDo: Samir Tags Checkboxes but I will change this later isA to autocomplete--}}
                {{--Tags checkboxes--}}
                <div>
                    <h4>Tags:</h4>
                    @foreach ($tags as $tag)
                        <div class="checkbox filters-checkbox-div">
                            <label>
                                <input type="checkbox" {{ in_array($tag->id, Request::get('tags', [])) ? 'checked' : '' }}
                                       name="{{ Constants::APPLIED_FILTERS_TAGS_IDS }}[]"
                                       value="{{ $tag->id }}">
                                {{ $tag->name }}
                            </label>
                        </div>
                    @endforeach
                </div>

                <hr/>

                {{--Apply filters & Clear buttons--}}
                <p>
                    <input class="btn btn-default" type="submit" value="Apply Filters"/>
                    <a href="{{ Request::url() }}" class="btn text-dark btn-link text-muted pull-right">Clear</a>
                </p>
            </form>
        </div>
    </div>
</div>
