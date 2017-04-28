<?php

use App\Utilities\Constants;

// Profile routes...
// ToDo: @Abzo auth middleware required, choose better route
Route::get('profile/edit', 'UserController@edit')
    ->name(Constants::ROUTES_PROFILE_EDIT);

// ToDo: @Abzo auth middleware required, choose better route
Route::post('profile/edit', 'UserController@editProfile')
    ->name(Constants::ROUTES_PROFILE_UPDATE);

Route::get('profile/{user}', 'UserController@index')
    ->name(Constants::ROUTES_PROFILE);

// TODO: @Abzo add problems, contests, groups and blogs like Teams below

// User teams
Route::get('profile/{user}/teams', 'TeamController@index')
    ->name(Constants::ROUTES_PROFILE_TEAMS);