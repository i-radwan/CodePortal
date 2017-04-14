<?php

namespace App\Models;

use App\Utilities\Constants;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Constants::TBL_USERS;

    /**
     * The primary key of the table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = Constants::FLD_USERS_ID;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        Constants::FLD_USERS_EMAIL,
        Constants::FLD_USERS_PASSWORD,
        Constants::FLD_USERS_USERNAME,
        Constants::FLD_USERS_GENDER,
        Constants::FLD_USERS_BIRTHDATE,
        Constants::FLD_USERS_PROFILE_PICTURE,
        Constants::FLD_USERS_COUNTRY
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        Constants::FLD_USERS_PASSWORD,
        Constants::FLD_USERS_REMEMBER_TOKEN,
    ];

    /**
     * Return the handles on different online judges of the current user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function handles()
    {
        return $this->belongsToMany(
            Judge::class,
            Constants::TBL_USER_HANDLES,
            Constants::FLD_USER_HANDLES_USER_ID,
            Constants::FLD_USER_HANDLES_JUDGE_ID
        )->withPivot(Constants::FLD_USER_HANDLES_HANDLE);
    }

    /**
     * Return user's handle corresponding to the given judge, if not found then null is returned
     *
     * @param Judge $judge
     * @return string|null
     */
    public function handle(Judge $judge)
    {
        $judgeHandle = $this->handles()->where(Constants::FLD_USER_HANDLES_JUDGE_ID, $judge->id)->first();

        if (!$judgeHandle) {
            return null;
        }

        return $judgeHandle->pivot->handle;
    }

    /**
     * Attach the given online judge handle to the current user
     *
     * @param int $judgeId
     * @param string $handle
     */
    public function addHandle($judgeId, $handle)
    {
        // TODO: Omar Wael
        // TODO: add these handles in a queue to fetch their submissions
        // TODO: update handle
        // TODO: validate
        $this->handles()->attach($judgeId, [Constants::FLD_USER_HANDLES_HANDLE => $handle]);
    }

    /**
     * Return all the submission of the current user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function submissions()
    {
        return $this->hasMany(Submission::class, Constants::FLD_SUBMISSIONS_USER_ID);
    }

    /**
     * Return the contests that the current user participated in
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function participatingContests()
    {
        return $this->belongsToMany(
            Contest::class,
            Constants::TBL_CONTEST_PARTICIPANTS,
            Constants::FLD_CONTEST_PARTICIPANTS_USER_ID,
            Constants::FLD_CONTEST_PARTICIPANTS_CONTEST_ID
        )->withTimestamps();
    }

    /**
     * Return the contests that the current user organized as admin
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function organizingContests()
    {
        return $this->belongsToMany(
            Contest::class,
            Constants::TBL_CONTEST_ADMINS,
            Constants::FLD_CONTEST_ADMINS_ADMIN_ID,
            Constants::FLD_CONTEST_ADMINS_CONTEST_ID
        );
    }

    /**
     * Return the contests that the current user owns
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function owningContests()
    {
        return $this->hasMany(Contest::class, Constants::FLD_CONTESTS_OWNER_ID);
    }

    /**
     * Return all questions asked by the current user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questions()
    {
        return $this->hasMany(Question::class, Constants::FLD_QUESTIONS_USER_ID);
    }

    /**
     * Return all user questions in specific contest
     *
     * @param int $contestId
     * @return mixed
     */
    public function contestQuestions($contestId)
    {
        return $this->hasMany(Question::class, Constants::FLD_QUESTIONS_USER_ID)
            ->where(Constants::FLD_QUESTIONS_CONTEST_ID, '=', $contestId);
    }

    /**
     * Return all questions answered by this contest organizer
     *
     * ToDo: Remove if not used
     *
     * @return mixed
     */
    public function answeredQuestions()
    {
        return $this->hasMany(Question::class)->where(Constants::FLD_QUESTIONS_ADMIN_ID, '=', $this->id);
    }


    /**
     * Return the notifications sent by this user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sentNotifications()
    {
        return $this->hasMany(Notification::class, Constants::FLD_NOTIFICATIONS_SENDER_ID);
    }

    /**
     * Return the notifications sent by this user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function receivedNotifications()
    {
        return $this->hasMany(Notification::class, Constants::FLD_NOTIFICATIONS_RECEIVER_ID);
    }

    /**
     * Return user unread notifications
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function unreadNotifications()
    {
        return $this->receivedNotifications()
            ->where(Constants::FLD_NOTIFICATIONS_STATUS, '=',
                Constants::NOTIFICATION_STATUS[Constants::NOTIFICATION_STATUS_UNREAD]);
    }
}
