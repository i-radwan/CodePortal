<?php

namespace App\Models;

use App\Exceptions\GroupInvitationException;
use App\Utilities\Constants;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use ValidateModelData;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Constants::TBL_NOTIFICATIONS;

    /**
     * The primary key of the table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = Constants::FLD_NOTIFICATIONS_ID;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        Constants::FLD_NOTIFICATIONS_SENDER_ID,
        Constants::FLD_NOTIFICATIONS_RECEIVER_ID,
        Constants::FLD_NOTIFICATIONS_RESOURCE_ID,
        Constants::FLD_NOTIFICATIONS_TYPE,
        Constants::FLD_NOTIFICATIONS_STATUS
    ];

    /**
     * The rules to check against before saving the model
     *
     * @var array
     */
    protected $rules = [
        Constants::FLD_NOTIFICATIONS_SENDER_ID => 'required|exists:' . Constants::TBL_USERS . ',' . Constants::FLD_USERS_ID,
        Constants::FLD_NOTIFICATIONS_RECEIVER_ID => 'required|exists:' . Constants::TBL_USERS . ',' . Constants::FLD_USERS_ID,
        Constants::FLD_NOTIFICATIONS_RESOURCE_ID => 'required|resource_exists_in_table',
        Constants::FLD_NOTIFICATIONS_TYPE => 'required|Regex:/([012])/'
    ];

    /**
     * Create and save new notification
     *
     * Notification maker function.
     *
     * @param array $attributes
     * @param User $sender
     * @param User $receiver
     * @param Team /Group/Contest $resource
     * @param int $type
     * @param bool $duplicationAllowed , allow resending the same notification to the same user twice
     * @throws GroupInvitationException
     */
    public static function make($attributes, User $sender, User $receiver, $resource, $type, $duplicationAllowed = false)
    {
        if ($sender != null && $receiver != null && $resource != null && $type != null) {

            // Check if user already received this notification
            if (!$duplicationAllowed) {

                // Get same resource notifications count
                $prevNotificationsCount = $receiver->receivedNotifications()
                    ->where(Constants::FLD_NOTIFICATIONS_RESOURCE_ID, '=', $resource->id)
                    ->where(Constants::FLD_NOTIFICATIONS_TYPE, '=', $type)->count();

                if ($prevNotificationsCount > 0)
                    throw new GroupInvitationException(
                        Constants::GROUP_INVITATION_EXCEPTION_MSGS
                        [Constants::GROUP_INVITATION_EXCEPTION_INVITED]); // Stop and prevent saving new one
            }

            // Save the notification after checking the duplication
            $notification = new Notification($attributes);
            $notification->sender()->associate($sender);
            $notification->receiver()->associate($receiver);
            $notification->resource()->associate($resource);
            $notification[Constants::FLD_NOTIFICATIONS_TYPE] = $type;
            $notification->save();
        }
    }


    /**
     * Returns the user who sent this notification
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sender()
    {
        return $this->belongsTo(User::class, Constants::FLD_NOTIFICATIONS_SENDER_ID);
    }

    /**
     * Returns the user who has to receive this notification
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, Constants::FLD_NOTIFICATIONS_RECEIVER_ID);
    }

    /**
     * Returns the resource that this notification points to it (group, team, ...etc)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function resource()
    {
        return $this->belongsTo(User::class, Constants::FLD_NOTIFICATIONS_RESOURCE_ID);
    }
}
