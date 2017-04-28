<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\Notification;
use App\Utilities\Constants;

class NotificationController extends Controller
{
    /**
     * Mark all current user unread notifications as read
     *
     * @return \Illuminate\Http\Response
     */
    public function markAllUserNotificationsRead()
    {
        $user = Auth::user();

        if ($user) {
            $user->receivedNotifications()
                // Get only unread ones
                ->ofStatus(Constants::NOTIFICATION_STATUS_UNREAD)
                // Mark as read
                ->update([Constants::FLD_NOTIFICATIONS_STATUS => Constants::NOTIFICATION_STATUS_READ]);
        }

        // Return success response
        return response()->make();
    }

    public function deleteNotification(Notification $notification)
    {
        $user = Auth::user();

        // Find the notification and update status
        if ($user && $user->receivedNotifications()->find($notification)) {
            $notification->delete();
        }

        // Return success response
        return response()->make();
    }
}
