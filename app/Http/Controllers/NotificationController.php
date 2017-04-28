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
    public function markAllRead()
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

    /**
     * Remove the specified notification from storage
     *
     * @param Notification $notification
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notification $notification)
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
