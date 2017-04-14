<?php

namespace App\Models;

use DB;
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
     * The basic database columns to be selected when getting the contest submissions
     *
     * @var array
     */
    protected $basicContestSubmissionsQueryCols = [
        Constants::TBL_USERS . '.' . Constants::FLD_USERS_USERNAME,
        Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_NAME . ' as ' . Constants::FLD_SUBMISSIONS_PROBLEM_NAME,
        Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_JUDGE_ID,
        Constants::TBL_SUBMISSIONS . '.' . Constants::FLD_SUBMISSIONS_JUDGE_SUBMISSION_ID,
        Constants::TBL_SUBMISSIONS . '.' . Constants::FLD_SUBMISSIONS_SUBMISSION_TIME,
        Constants::TBL_SUBMISSIONS . '.' . Constants::FLD_SUBMISSIONS_EXECUTION_TIME,
        Constants::TBL_SUBMISSIONS . '.' . Constants::FLD_SUBMISSIONS_CONSUMED_MEMORY,
        Constants::TBL_SUBMISSIONS . '.' . Constants::FLD_SUBMISSIONS_VERDICT,
        Constants::TBL_LANGUAGES . '.' . Constants::FLD_LANGUAGES_NAME . ' as ' . Constants::FLD_SUBMISSIONS_LANGUAGE_NAME
    ];

    /**
     * Return public visible contests only
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeOfPublic(Builder $query)
    {
        return $query->where(
            Constants::FLD_CONTESTS_VISIBILITY,
            '=',
            Constants::CONTEST_VISIBILITY[Constants::CONTEST_VISIBILITY_PUBLIC_KEY]
        );
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
     * Return all submissions of the current contest
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function submissions()
    {
        $query = DB::table(Constants::TBL_CONTESTS)
            ->select($this->basicContestSubmissionsQueryCols)
            ->where(
                Constants::TBL_CONTESTS . '.' . Constants::FLD_CONTESTS_ID,
                '=',
                $this->id
            );

        $this->contestJoinProblems($query);
        $this->contestJoinUsers($query);
        $this->contestJoinSubmissions($query);

        return $query;
    }

    /**
     * Join contest with its related problems
     *
     * @param \Illuminate\Database\Query\Builder $query
     */
    private function contestJoinProblems($query)
    {
        $query
            ->join(
                Constants::TBL_CONTEST_PROBLEMS,
                Constants::TBL_CONTEST_PROBLEMS . '.' . Constants::FLD_CONTEST_PROBLEMS_CONTEST_ID,
                '=',
                Constants::TBL_CONTESTS . '.' . Constants::FLD_CONTESTS_ID
            )
            ->join(
                Constants::TBL_PROBLEMS,
                Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_ID,
                '=',
                Constants::TBL_CONTEST_PROBLEMS . '.' . Constants::FLD_CONTEST_PROBLEMS_PROBLEM_ID
            );
    }

    /**
     * Join contest with its related users
     *
     * @param \Illuminate\Database\Query\Builder $query
     */
    private function contestJoinUsers($query)
    {
        $query
            ->join(
                Constants::TBL_CONTEST_PARTICIPANTS,
                Constants::TBL_CONTEST_PARTICIPANTS . '.' . Constants::FLD_CONTEST_PARTICIPANTS_CONTEST_ID,
                '=',
                Constants::TBL_CONTESTS . '.' . Constants::FLD_CONTESTS_ID
            )
            ->join(
                Constants::TBL_USERS,
                Constants::TBL_USERS . '.' . Constants::FLD_USERS_ID,
                '=',
                Constants::TBL_CONTEST_PARTICIPANTS . '.' . Constants::FLD_CONTEST_PARTICIPANTS_USER_ID
            );
    }

    /**
     * Join contest with its related submissions
     *
     * @param \Illuminate\Database\Query\Builder $query
     */
    private function contestJoinSubmissions($query)
    {
        $contestStartTime = strtotime($this->time);
        $contestEndTime = strtotime($this->time . ' + ' . $this->duration . ' minute');

        // TODO: need to check timestamps accurately
        // TODO: It seems that Codeforces timestamp is leading 4 hours
        $query
            ->join(
                Constants::TBL_SUBMISSIONS,
                function ($join) {
                    $join->on(
                        Constants::TBL_SUBMISSIONS . '.' . Constants::FLD_SUBMISSIONS_USER_ID,
                        '=',
                        Constants::TBL_USERS . '.' . Constants::FLD_USERS_ID
                    );
                    $join->on(
                        Constants::TBL_SUBMISSIONS . '.' . Constants::FLD_SUBMISSIONS_PROBLEM_ID,
                        '=',
                        Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_ID
                    );
                }
            )
            ->join(
                Constants::TBL_LANGUAGES,
                Constants::TBL_LANGUAGES . '.' . Constants::FLD_LANGUAGES_ID,
                '=',
                Constants::TBL_SUBMISSIONS . '.' . Constants::FLD_SUBMISSIONS_LANGUAGE_ID
            )
            ->whereBetween(
                Constants::TBL_SUBMISSIONS . '.' . Constants::FLD_SUBMISSIONS_SUBMISSION_TIME,
                [$contestStartTime, $contestEndTime]
            )
            ->orderByDesc(
                Constants::TBL_SUBMISSIONS . '.' . Constants::FLD_SUBMISSIONS_SUBMISSION_TIME
            );
    }


    /**
     * Return all participating users of the current contest
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function participants()
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
    public function organizers()
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
     * Return contest announcements
     *
     * @return mixed
     */
    public function announcements()
    {
        return $this->questions()->where(
            Constants::FLD_QUESTIONS_STATUS,
            '=',
            Constants::QUESTION_STATUS[Constants::QUESTION_STATUS_ANNOUNCEMENT_KEY]
        );
    }

    /**
     * Check if contest is currently running
     *
     * @return bool
     */
    public function isRunning()
    {
        // Get contest end time by adding its duration to its start time
        $contestEndTime = strtotime($this->time . ' + ' . $this->duration . ' minute');

        // Check if contest is running
        return (date("Y-m-d H:i:s") > $this->time && date("Y-m-d H:i:s") < date("Y-m-d H:i:s", $contestEndTime));
    }

    /**
     * Return the notifications pointing at this contest
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class, Constants::FLD_NOTIFICATIONS_RESOURCE_ID);
    }
}
