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
| NOTE:: Routes order MATTERS
|
*/

// Homepage routes...
Route::get('/', 'HomeController@index');

include 'web/problems.php';
include 'web/contests.php';
include 'web/groups.php';
include 'web/teams.php';
include 'web/blogs.php';
include 'web/profile.php';
include 'web/notifications.php';
include 'web/auth.php';
include 'web/errors.php';