<?php

namespace App\Models;

use App\Utilities\Constants;
use App\Exceptions\InvitationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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
        Constants::FLD_NOTIFICATIONS_TYPE,
        Constants::FLD_NOTIFICATIONS_STATUS
    ];

    /**
     * The rules to check against before saving the model
     *
     * TODO: recheck all validations
     *
     * @var array
     */
    protected $rules = [
        Constants::FLD_NOTIFICATIONS_SENDER_ID => 'required|exists:' . Constants::TBL_USERS . ',' . Constants::FLD_USERS_ID,
        Constants::FLD_NOTIFICATIONS_RECEIVER_ID => 'required|exists:' . Constants::TBL_USERS . ',' . Constants::FLD_USERS_ID,
        //Constants::FLD_NOTIFICATIONS_RESOURCE_ID => 'required|resource_exists_in_table',
        Constants::FLD_NOTIFICATIONS_TYPE => 'required|Regex:/([012])/'
    ];

    /**
     * Create and save new notification
     * Notification maker function.
     *
     * @param User $sender
     * @param User $receiver
     * @param Model|Team|Group|Contest $resource
     * @param int $type
     * @param bool $duplicationAllowed Whether resending the same notification to the same user twice is allowed
     * @throws InvitationException
     */
    public static function make(User $sender, User $receiver, Model $resource, $type, $duplicationAllowed = false)
    {
        try {
            if ($sender == null || $receiver == null || $resource == null || $type == null) {
                return;
            }

            // Check if user already received this notification
            if (!$duplicationAllowed) {
                // Get same resource notifications count
                $prevNotificationsCount = $receiver->receivedNotifications()
                    ->where(Constants::FLD_NOTIFICATIONS_RESOURCE_ID, '=', $resource->id)
                    ->where(Constants::FLD_NOTIFICATIONS_TYPE, '=', $type)
                    ->count();

                if ($prevNotificationsCount > 0) {
                    // Stop and prevent saving new one
                    throw new InvitationException(
                        Constants::INVITATION_EXCEPTION_MSGS[Constants::INVITATION_EXCEPTION_INVITED]
                    );
                }
            }

            // Save the notification after checking the duplication
            $notification = new Notification();
            $notification[Constants::FLD_NOTIFICATIONS_STATUS] = Constants::NOTIFICATION_STATUS_UNREAD;
            $notification[Constants::FLD_NOTIFICATIONS_TYPE] = $type;
            $notification->sender()->associate($sender);
            $notification->receiver()->associate($receiver);
            $notification->resource()->associate($resource);
            $notification->save();
        } catch (InvitationException $e) {
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|null
     */
    public function resource()
    {
        switch ($this[Constants::FLD_NOTIFICATIONS_TYPE]) {
            case Constants::NOTIFICATION_TYPE_CONTEST:
                $class = Contest::class;
                break;
            case Constants::NOTIFICATION_TYPE_GROUP:
                $class = Group::class;
                break;
            case Constants::NOTIFICATION_TYPE_TEAM:
                $class = Team::class;
                break;
            default:
                return null;
        }

        return $this->belongsTo($class, Constants::FLD_NOTIFICATIONS_RESOURCE_ID);
    }

    /**
     * Scope a query to only include notifications of certain type
     *
     * @param Builder $query
     * @param string|null $type
     * @return Builder
     */
    public function scopeOfType(Builder $query, $type = null)
    {
        if ($type == null) {
            return $query;
        }

        $query->where(
            Constants::TBL_NOTIFICATIONS . '.' . Constants::FLD_NOTIFICATIONS_TYPE,
            '=',
            $type
        );

        return $query;
    }

    /**
     * Scope a query to only include notifications of certain status
     *
     * @param Builder $query
     * @param string|null $status
     * @return Builder
     */
    public function scopeOfStatus(Builder $query, $status = null)
    {
        if ($status == null) {
            return $query;
        }

        $query->where(
            Constants::TBL_NOTIFICATIONS . '.' . Constants::FLD_NOTIFICATIONS_STATUS,
            '=',
            $status
        );

        return $query;
    }

    /**
     * Scope a query to only include notifications of certain sender
     *
     * @param Builder $query
     * @param string|null $senderID
     * @return Builder
     */
    public function scopeOfSender(Builder $query, $senderID = null)
    {
        if ($senderID == null) {
            return $query;
        }

        $query->where(
            Constants::TBL_NOTIFICATIONS . '.' . Constants::FLD_NOTIFICATIONS_SENDER_ID,
            '=',
            $senderID
        );

        return $query;
    }

    /**
     * Scope a query to only include notifications of certain receiver
     *
     * @param Builder $query
     * @param string|null $receiverID
     * @return Builder
     */
    public function scopeOfReceiver(Builder $query, $receiverID = null)
    {
        if ($receiverID == null) {
            return $query;
        }

        $query->where(
            Constants::TBL_NOTIFICATIONS . '.' . Constants::FLD_NOTIFICATIONS_RECEIVER_ID,
            '=',
            $receiverID
        );

        return $query;
    }

    /**
     * Scope a query to only include notifications of certain resource
     *
     * @param Builder $query
     * @param string|null $resourceID
     * @return Builder
     */
    public function scopeOfResource(Builder $query, $resourceID = null)
    {
        if ($resourceID == null) {
            return $query;
        }

        $query->where(
            Constants::TBL_NOTIFICATIONS . '.' . Constants::FLD_NOTIFICATIONS_RESOURCE_ID,
            '=',
            $resourceID
        );

        return $query;
    }
}
