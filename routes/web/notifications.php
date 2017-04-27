<?php

Route::group(['middleware' => 'auth'], function () {
    // Notifications routes...
    Route::put('notifications/mark_all_read', 'NotificationController@markAllUserNotificationsRead');

    Route::delete('notification/{notification}', 'NotificationController@deleteNotification');
});