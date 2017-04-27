<?php

// Profile routes...
Route::get('profile/{user}', 'UserController@index');
Route::get('edit', 'UserController@edit'); // ToDo: @Abzo auth middleware required, choose better route
Route::post('edit', 'UserController@editProfile');  // ToDo: @Abzo auth middleware required, choose better route

// Teams routes...
// TODO: to be merged with profile routes
Route::get('profile/{user}/teams', 'TeamController@index');