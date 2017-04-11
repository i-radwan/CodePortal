<div class="container filters-table-container">
    {{--{{ TODO: NOT YET MADE GENERIC}}--}}
    <div class="row">
        <div class="col-md-12">
            <form action="/problems" method="get">
                <!-- Search Bar -->
                <div id="custom-search-input">
                    <div class="input-group col-md-12">
                        <input type="text" class="form-control" placeholder="Problem Name"
                               action="/problem/show" name="q" value="{{$data[Constants::PREVIOUS_TABLE_FILTERS][Constants::APPLIED_FILTERS_SEARCH_STRING]}}">
                        <span class="input-group-btn">
                            <button class="btn btn-info btn-lg" type="submit">
                                <i class="glyphicon glyphicon-search"></i>
                            </button>
                        </span>
                    </div>
                </div>
                <!-- Judges Check Boxes -->
                <div>
                    <h4>Online Judges:</h4>
                    @foreach ($data[Constants::FILTERS_JUDGES] as $judge)
                        <div class="checkbox">
                            <label><input type="checkbox" value="{{$judge->id}}"
                                          name="judges[]" {{(in_array($judge->id, $data[Constants::PREVIOUS_TABLE_FILTERS][Constants::APPLIED_FILTERS_JUDGES_IDS]))?'checked':''}}> {{$judge->name}}
                            </label>
                        </div>
                    @endforeach
                </div>
                <!-- ToDo Samir Tags Checkboxes but I will change this later isA to autocomplete   -->
                <div>
                    <h4>Tags:</h4>
                    @foreach ($data[Constants::FILTERS_TAGS] as $tag)
                        <div class="checkbox filters-checkbox-div">
                            <label>
                                <input type="checkbox" value="{{$tag->id}}"
                                       name="{{Constants::APPLIED_FILTERS_TAGS_IDS}}[]" {{(in_array($tag->id, $data[Constants::PREVIOUS_TABLE_FILTERS][Constants::APPLIED_FILTERS_TAGS_IDS]))?'checked':''}}> {{$tag->name}}
                            </label>
                        </div>
                    @endforeach
                    <hr/>
                    <p>
                        <input type="submit" value="Apply Filters" class="btn btn-default"/>
                        <a href="/problems/{{Constants::PREVIOUS_TABLE_FILTERS}}" class="btn text-dark btn-link text-muted pull-right">Clear</a>
                    </p>
                </div>
            </form>
            <!-- Tags by autocomplete since it's the best here -->
        </div>
    </div>
</div>
