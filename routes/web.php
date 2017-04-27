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

include 'ProblemsRoutes.php';
include 'ContestsRoutes.php';
include 'GroupsRoutes.php';
include 'TeamsRoutes.php';
include 'BlogsRoutes.php';
include 'ProfileRoutes.php';
include 'NotificationsRoutes.php';
include 'AuthRoutes.php';
include 'ErrorsRoutes.php';