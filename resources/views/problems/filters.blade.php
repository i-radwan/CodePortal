<!DOCTYPE html>
<html>
   <head></head>
   <body>
      <div class="container">
         <div class="row">
            <div class="col-md-6">
            <!-- Search Bar -->
               <div id="custom-search-input">
                  <div class="input-group col-md-12">
                     <input type="text" class="form-control input-lg" placeholder="problem_name" action="/problem/tag=3" >
                     <span class="input-group-btn">
                     <button class="btn btn-info btn-lg" type="button" type="submit">
                     <i class="glyphicon glyphicon-search"></i>
                     </button>
                     </span>
                  </div>
               </div>
               <!-- Judges Check Boxes -->
               <div class = "container">
               @foreach ($data->judges as $judge)
               	<div class="checkbox">
  					<label><input type="checkbox" value=""> {{$judge->name}} </label>
				</div>
               @endforeach
               </div>
               <!-- Tags Checkboxes but I will change this later isA to autocomplete   --> 
               <div class = "container">
               @foreach ($data->tags as $tag)
               	<div class="checkbox">
  					<label><input type="checkbox" value=""> {{$tag->name}} </label>
				</div>
               @endforeach
               </div>
               <!-- Tags by autocomplete since it's the best here -->
            </div>
         </div>
      </div>
   </body>
</html>