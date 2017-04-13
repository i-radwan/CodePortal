<?php

namespace App\Models;

use App\Utilities\Constants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Contest extends Model
{
    use ValidateModelData;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Constants::TBL_CONTESTS;

    /**
     * The primary key of the table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = Constants::FLD_CONTESTS_ID;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        Constants::FLD_CONTESTS_NAME,
        Constants::FLD_CONTESTS_TIME,
        Constants::FLD_CONTESTS_DURATION,
        Constants::FLD_CONTESTS_VISIBILITY
    ];

    /**
     * The rules to check against before saving the model
     *
     * @var array
     */
    protected $rules = [
        Constants::FLD_CONTESTS_NAME => 'required|max:100',
        Constants::FLD_CONTESTS_OWNER_ID => 'required|exists:' . Constants::TBL_USERS . ',' . Constants::FLD_USERS_ID,
        Constants::FLD_CONTESTS_TIME => 'required|date_format:Y-m-d H:i:s|after:today',
        Constants::FLD_CONTESTS_DURATION => 'integer|required|min:1',
        Constants::FLD_CONTESTS_VISIBILITY => 'required|Regex:/([01])/'
    ];

    /**
     * Return public visible contests only
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeOfPublic(Builder $query)
    {
        $query->where(
            Constants::FLD_CONTESTS_VISIBILITY,
            '=',
            Constants::CONTEST_VISIBILITY[Constants::CONTEST_VISIBILITY_PUBLIC_KEY]
        );
        return $query;
    }

    /**
     * Return all problems of the current contest
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function problems()
    {
        return $this->belongsToMany(
            Problem::class,
            Constants::TBL_CONTEST_PROBLEMS,
            Constants::FLD_CONTEST_PROBLEMS_CONTEST_ID,
            Constants::FLD_CONTEST_PROBLEMS_PROBLEM_ID
        );
    }

    /**
     * Return all participating users of the current contest
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function participatingUsers()
    {
        return $this->belongsToMany(
            User::class,
            Constants::TBL_CONTEST_PARTICIPANTS,
            Constants::FLD_CONTEST_PARTICIPANTS_CONTEST_ID,
            Constants::FLD_CONTEST_PARTICIPANTS_USER_ID
        )->withTimestamps();
    }

    /**
     * Return the owner user of the current contest
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, Constants::FLD_CONTESTS_OWNER_ID);
    }

    /**
     * Return the organizing admins of the current contest
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function organizingUsers()
    {
        return $this->belongsToMany(
            User::class,
            Constants::TBL_CONTEST_ADMINS,
            Constants::FLD_CONTEST_ADMINS_CONTEST_ID,
            Constants::FLD_CONTEST_ADMINS_ADMIN_ID
        );
    }

    /**
     * Return the asked questions of the current contest
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questions()
    {
        return $this->hasMany(Question::class, Constants::FLD_QUESTIONS_CONTEST_ID);
    }

    /**
     * Return contest announcments
     * @return mixed
     */
    public function announcements()
    {
        return $this->questions()->where(Constants::FLD_QUESTIONS_STATUS, '=',
            Constants::QUESTION_STATUS[Constants::QUESTION_STATUS_ANNOUNCEMENT_KEY]);
    }

    /**
     * Check if contest is currently running
     * @return bool
     */
    public function isContestRunning()
    {
        // Get contest end time by adding its duration to its start time
        $contestEndTime = strtotime($this->time . ' + ' . $this->duration . ' minute');

        // Check if contest in running
        if (date("Y-m-d H:i:s") > $this->time
            && date("Y-m-d H:i:s") < date("Y-m-d H:i:s", $contestEndTime)
        )
            return true;
        return false;
    }
}
