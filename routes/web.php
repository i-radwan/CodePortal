<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Homepage routes...
Route::get('/', 'HomeController@index');

// Profile routes...
Route::get('profile/{user}', 'UserController@index');

// Contest routes...
Route::get('contests', 'ContestController@index');
Route::get('contest/edit', 'ContestController@addEditContestView');
Route::get('contest/add', 'ContestController@addEditContestView');
Route::get('contest/delete/{contestID}', 'ContestController@deleteContest');
Route::get('contest/leave/{contestID}', 'ContestController@leaveContest');
Route::get('contest/join/{contestID}', 'ContestController@joinContest');


Route::get('contest/{contestID}', 'ContestController@displayContest');


Route::post('contest/add', 'ContestController@addContest');
Route::post('contest/edit', 'ContestController@editContest');

// Question routes...
Route::get('contest/question/announce/{questionID}', 'ContestController@announceQuestion');
Route::get('contest/question/renounce/{questionID}', 'ContestController@renounceQuestion');
Route::post('contest/question/answer', 'ContestController@answerQuestion');
Route::post('contest/question/{contestID}', 'ContestController@addQuestion');

// Problems routes...
Route::get('problems', 'ProblemController@index');

// Blogs routes...
Route::get('blogs', 'BlogController@index');

// Groups routes...
Route::get('groups', 'GroupController@index');


// Authentication route definitions copied from function 'Auth::routes()'
// so we can easily edit them later if needed

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');
