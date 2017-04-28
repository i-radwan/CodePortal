<?php

use App\Utilities\Constants;

// Notifications routes...
Route::group(['middleware' => 'auth'], function () {
    // Mark all notifications of the current user as read
    Route::put('notifications/mark_all_read', 'NotificationController@markAllRead')
        ->name(Constants::ROUTES_NOTIFICATIONS_MARK_ALL_READ);

    // Delete the passed notification
    Route::delete('notifications/{notification}', 'NotificationController@destroy')
        ->name(Constants::ROUTES_NOTIFICATIONS_DELETE);
});