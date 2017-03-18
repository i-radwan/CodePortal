<!DOCTYPE html>
<html>
<head></head>
<body>
<div class="container filters-table-container">
    <div class="row">
        <div class="col-md-12">
            <form action="/problems" method="get">
                <!-- Search Bar -->
                <div id="custom-search-input">
                    <div class="input-group col-md-12">
                        <input type="text" class="form-control input-lg" placeholder="Problem Name" action="/problem/show" name="q" >
                        <span class="input-group-btn">
                     <button class="btn btn-info btn-lg"  type="submit">
                     <i class="glyphicon glyphicon-search"></i>
                     </button>
                     </span>
                    </div>
                </div>
                <!-- Judges Check Boxes -->
                <div class = "container">
                    <h3>Online Judges:</h3>
                    @foreach ($data->judges as $judge)
                        <div class="checkbox">
                            <label><input type="checkbox" value="{{$judge->id}}" name="judges[]" > {{$judge->name}} </label>
                        </div>
                    @endforeach
                </div>
                <!-- Tags Checkboxes but I will change this later isA to autocomplete   -->
                <div class = "container">
                    <h3>Tags:</h3>
                    @foreach ($data->tags as $tag)
                        <div class="checkbox">
                            <label><input type="checkbox" value="{{$tag->id}}"  name="tags[]"> {{$tag->name}} </label>
                        </div>
                    @endforeach
                        <p><input type="submit" value="Apply Filters" class="btn btn-default btn-lg" /></p>
                </div>
            </form>
            <!-- Tags by autocomplete since it's the best here -->
        </div>
    </div>
</div>
</body>
</html>