<?php

use App\Utilities\Constants;

// Profile routes...
Route::group(['middleware' => 'auth'], function () {
    Route::get('profile/edit', 'UserController@edit')
        ->name(Constants::ROUTES_PROFILE_EDIT);

    Route::post('profile/edit', 'UserController@update')
        ->name(Constants::ROUTES_PROFILE_UPDATE);
});

// User basic info
Route::get('profile/{user}', 'UserController@displayUserInfo')
    ->name(Constants::ROUTES_PROFILE);

// User solved problems
Route::get('profile/{user}/problems', 'UserController@displayUserSolvedProblems')
    ->name(Constants::ROUTES_PROFILE_PROBLEMS);

// User solved problems
Route::get('profile/{user}/problems/solved', 'UserController@displayUserSolvedProblems')
    ->name(Constants::ROUTES_PROFILE_PROBLEMS_SOLVED);

// User un-solved problems
Route::get('profile/{user}/problems/unsolved', 'UserController@displayUserUnSolvedProblems')
    ->name(Constants::ROUTES_PROFILE_PROBLEMS_UNSOLVED);

// User contests
Route::get('profile/{user}/contests', 'UserController@displayUserContests')
    ->name(Constants::ROUTES_PROFILE_CONTESTS);

// User groups
Route::get('profile/{user}/groups', 'UserController@displayUserGroups')
    ->name(Constants::ROUTES_PROFILE_GROUPS);

// User teams
Route::get('profile/{user}/teams', 'TeamController@index')
    ->name(Constants::ROUTES_PROFILE_TEAMS);

// User blogs
Route::get('profile/{user}/blogs', 'Blogs\BlogController@displayUserPosts')
    ->name(Constants::ROUTES_PROFILE_BLOGS);