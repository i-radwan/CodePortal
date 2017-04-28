<?php

use App\Utilities\Constants;

// Errors Routes...
Route::get('errors/404', function () {
    return view('errors.404')->with('pageTitle', 'CodePortal | 404');
})->name(Constants::ROUTES_ERRORS_404);

Route::get('errors/401', function () {
    return view('errors.401')->with('pageTitle', 'CodePortal | 401');
})->name(Constants::ROUTES_ERRORS_401);
