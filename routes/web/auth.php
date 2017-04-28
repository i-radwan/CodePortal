<?php

use App\Utilities\Constants;

// Authentication route definitions copied from function 'Auth::routes()'
// so we can easily edit them later if needed

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name(Constants::ROUTES_AUTH_LOGIN);
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name(Constants::ROUTES_AUTH_LOGOUT);

// Registration Routes...
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name(Constants::ROUTES_AUTH_REGISTER);
Route::post('register', 'Auth\RegisterController@register');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name(Constants::ROUTES_AUTH_PASSWORD_REQUEST);
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name(Constants::ROUTES_AUTH_PASSWORD_EMAIL);
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name(Constants::ROUTES_AUTH_PASSWORD_RESET);
Route::post('password/reset', 'Auth\ResetPasswordController@reset');