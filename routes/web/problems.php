<?php

use App\Utilities\Constants;

// Problems routes...
Route::get('problems', 'Problems\ProblemController@index')->name(Constants::ROUTES_PROBLEMS_INDEX);