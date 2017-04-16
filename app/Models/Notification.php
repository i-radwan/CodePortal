<?php

namespace App\Models;

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
     * ToDo @IAR custom validation for resource_id (must exists in one of the resources tables)
     *
     * @var array
     */
    protected $rules = [
        Constants::FLD_NOTIFICATIONS_SENDER_ID => 'required|exists:' . Constants::TBL_USERS . ',' . Constants::FLD_USERS_ID,
        Constants::FLD_NOTIFICATIONS_RECEIVER_ID => 'required|exists:' . Constants::TBL_USERS . ',' . Constants::FLD_USERS_ID,
        Constants::FLD_NOTIFICATIONS_TYPE => 'required|Regex:/([012])/'
    ];

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
