<?php

use App\Utilities\Constants;

// Profile routes...
Route::group(['middleware' => 'auth'], function () {
    Route::get('profile/edit', 'UserController@edit')
        ->name(Constants::ROUTES_PROFILE_EDIT);

    Route::post('profile/edit', 'UserController@update')
        ->name(Constants::ROUTES_PROFILE_UPDATE);
});

Route::get('profile/{user}', 'UserController@index')
    ->name(Constants::ROUTES_PROFILE);

// TODO: @Abzo add problems, contests, groups and blogs like Teams below

// User teams
Route::get('profile/{user}/teams', 'TeamController@index')
    ->name(Constants::ROUTES_PROFILE_TEAMS);

// User blogs
Route::get('profile/{user}/blogs', 'BlogController@displayUserPosts')
    ->name(Constants::ROUTES_PROFILE_BLOGS);