<?php

namespace App\Http\Controllers;

use Auth;
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
                ->where(Constants::FLD_NOTIFICATIONS_STATUS, '=',
                    [Constants::FLD_NOTIFICATIONS_STATUS =>
                        Constants::NOTIFICATION_STATUS[Constants::NOTIFICATION_STATUS_UNREAD]])
                // Mark as read
                ->update([Constants::FLD_NOTIFICATIONS_STATUS =>
                    Constants::NOTIFICATION_STATUS[Constants::NOTIFICATION_STATUS_READ]]);
        }

        // Return success response
        return response()->make();
    }
}
