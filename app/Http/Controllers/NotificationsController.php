<?php

namespace App\Http\Controllers;

use App\Utilities\Constants;
use Auth;

class NotificationsController extends Controller
{
    public function markAllUserNotificationsRead(\Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->receivedNotifications()->update([Constants::FLD_NOTIFICATIONS_STATUS =>
                Constants::NOTIFICATION_STATUS[Constants::NOTIFICATION_STATUS_READ]]);
        }
        return response()->make();
    }
}
