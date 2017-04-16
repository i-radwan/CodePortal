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
     * The basic database columns to be selected when getting the contest problems with statistics
     *
     * @var array
     */
    protected $basicContestProblemsQueryCols = [
        Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_ID,
        Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_JUDGE_ID,
        Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_JUDGE_FIRST_KEY,
        Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_JUDGE_SECOND_KEY,
        Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_NAME
    ];

    /**
     * The basic database columns to be selected when getting the contest users with statistics
     *
     * @var array
     */
    protected $basicContestUsersQueryCols = [
        Constants::TBL_USERS . '.' . Constants::FLD_USERS_ID . ' as ' . Constants::FLD_SUBMISSIONS_USER_ID,
        Constants::TBL_USERS . '.' . Constants::FLD_USERS_USERNAME
    ];

    /**
     * The basic database columns to be selected when getting the contest users problems statistics
     *
     * @var array
     */
    protected $basicContestUsersProblemsQueryCols = [
        Constants::TBL_USERS . '.' . Constants::FLD_USERS_ID . ' as ' . Constants::FLD_SUBMISSIONS_USER_ID,
        Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_ID . ' as ' . Constants::FLD_SUBMISSIONS_PROBLEM_ID,
        Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_JUDGE_ID,
        Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_JUDGE_FIRST_KEY,
        Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_JUDGE_SECOND_KEY,
        Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_NAME . ' as ' . Constants::FLD_SUBMISSIONS_PROBLEM_NAME
    ];

    /**
     * The basic database columns to be selected when getting the contest standings
     *
     * @var array
     */
    protected $basicContestStandingsQueryCols = [
        Constants::TBL_CONTEST_PARTICIPANTS . '.' . Constants::FLD_CONTEST_PARTICIPANTS_USER_ID,
        Constants::TBL_USERS . '.' . Constants::FLD_USERS_USERNAME,
        Constants::TBL_CONTEST_PROBLEMS . '.' . Constants::FLD_CONTEST_PROBLEMS_PROBLEM_ID,
        Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_NAME . ' as ' . Constants::FLD_SUBMISSIONS_PROBLEM_NAME,
        Constants::TBL_SUBMISSIONS . '.' . Constants::FLD_SUBMISSIONS_VERDICT,
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
     * Return the notifications pointing at this contest
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class, Constants::FLD_NOTIFICATIONS_RESOURCE_ID);
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
     * Return the problems of the current contest along with
     * the total number of submissions and the number of accepted submissions per problem
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function problemStatistics()
    {
        $query = $this->contestBasicQuery($this->basicContestProblemsQueryCols);

        // Calculate number of accepted submissions
        $query->addSelect(DB::raw(
            "sum(case when " .
            "`" . Constants::TBL_SUBMISSIONS . "`.`" . Constants::FLD_SUBMISSIONS_VERDICT . "` " .
            "= " .
            "'" . Constants::VERDICT_ACCEPTED . "' " .
            "then 1 else 0 end) as " .
            "`" . Constants::FLD_PROBLEMS_SOLVED_COUNT . "`"
        ));

        // Calculate total number of submissions
        $query->addSelect(DB::raw(
            "sum(case when " .
            "`" . Constants::TBL_SUBMISSIONS . "`.`" . Constants::FLD_SUBMISSIONS_VERDICT . "` " .
            "is not null " .
            "then 1 else 0 end) as " .
            "`" . Constants::FLD_PROBLEMS_TRAILS_COUNT . "`"
        ));

        $this->contestJoinProblems($query);
        $this->contestJoinUsers($query);
        $this->contestLeftJoinSubmissions($query);

        $query->groupBy(Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_ID);
        $query->orderBy(Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_ID);    // TODO: order by contest owner problems order

        return $query;
    }

    /**
     * Return the users of the current contest along with
     * the number of solved problems, the number of wrong submissions, and the penalty
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function userStatistics()
    {
        $query = $this->contestBasicQuery($this->basicContestUsersQueryCols);

        // Calculate number of accepted submissions
        $query->addSelect(DB::raw(
            "sum(case when " .
            "`" . Constants::TBL_SUBMISSIONS . "`.`" . Constants::FLD_SUBMISSIONS_VERDICT . "` " .
            "= " .
            "'" . Constants::VERDICT_ACCEPTED . "' " .
            "then 1 else 0 end) as " .
            "`" . Constants::FLD_USERS_SOLVED_COUNT . "`"
        ));

        // Calculate total number of submissions
        $query->addSelect(DB::raw(
            "sum(case when " .
            "`" . Constants::TBL_SUBMISSIONS . "`.`" . Constants::FLD_SUBMISSIONS_VERDICT . "` " .
            "is not null " .
            "then 1 else 0 end) as " .
            "`" . Constants::FLD_USERS_TRAILS_COUNT . "`"
        ));

        // Calculate the penalty of the user
        $query->addSelect(DB::raw(
            "sum(case when " .
            "`" . Constants::TBL_SUBMISSIONS . "`.`" . Constants::FLD_SUBMISSIONS_VERDICT . "` " .
            "= " .
            "'" . Constants::VERDICT_ACCEPTED . "' " .
            "then " .
            "`" . Constants::TBL_SUBMISSIONS . "`.`" . Constants::FLD_SUBMISSIONS_VERDICT . "` " .
            "- " .
            "UNIX_TIMESTAMP(" .
            "`" . Constants::TBL_CONTESTS . "`.`" . Constants::FLD_CONTESTS_TIME . "`" .
            ") " .
            "else " .
            Constants::CONTESTS_PENALTY_PER_WRONG_SUBMISSION . " ".
            "end) as " .
            "`" . Constants::FLD_USERS_PENALTY . "`"
        ));

        $this->contestJoinProblems($query);
        $this->contestJoinUsers($query);
        $this->contestLeftJoinSubmissionsUntilFirstAccepted($query);
        $query->groupBy(Constants::TBL_USERS . '.' . Constants::FLD_USERS_ID);

        return $query;
    }

    /**
     * Return statistics about the users and their submission
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function userProblemStatistics()
    {
        $query = $this->contestBasicQuery($this->basicContestUsersProblemsQueryCols);

        // Calculate number of accepted submissions
        $query->addSelect(DB::raw(
            "sum(case when " .
            "`" . Constants::TBL_SUBMISSIONS . "`.`" . Constants::FLD_SUBMISSIONS_VERDICT . "` " .
            "= " .
            "'" . Constants::VERDICT_ACCEPTED . "' " .
            "then 1 else 0 end) as " .
            "`" . Constants::FLD_PROBLEMS_SOLVED_COUNT . "`"
        ));

        // Calculate total number of submissions
        $query->addSelect(DB::raw(
            "sum(case when " .
            "`" . Constants::TBL_SUBMISSIONS . "`.`" . Constants::FLD_SUBMISSIONS_VERDICT . "` " .
            "is not null " .
            "then 1 else 0 end) as " .
            "`" . Constants::FLD_PROBLEMS_TRAILS_COUNT . "`"
        ));

        $this->contestJoinProblems($query);
        $this->contestJoinUsers($query);
        $this->contestLeftJoinSubmissionsUntilFirstAccepted($query);

        $query->groupBy(Constants::TBL_USERS . '.' . Constants::FLD_USERS_ID);
        $query->groupBy(Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_ID);

        return $query;
    }

    /**
     * Return the standings of the current contest
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function standings()
    {
        $t1 = $this->userStatistics();
        $t2 = $this->userProblemStatistics();

        $query = DB::table(DB::raw(
            "(" . $t1->toSql() . ") as `t1` natural join (" . $t2->toSql() . ") as `t2`"
        ));

        $query->orderByDesc(Constants::FLD_USERS_SOLVED_COUNT);
        $query->orderBy(Constants::FLD_USERS_PENALTY);
        $query->orderBy(Constants::FLD_USERS_TRAILS_COUNT);
        $query->orderBy(Constants::FLD_SUBMISSIONS_PROBLEM_ID);

        return $query;
    }

    /**
     * Return all submissions of the current contest
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function submissions()
    {
        $query = $this->contestBasicQuery($this->basicContestSubmissionsQueryCols);
        $this->contestJoinProblems($query);
        $this->contestJoinUsers($query);
        $this->contestJoinSubmissions($query);

        $query
            ->join(
                Constants::TBL_LANGUAGES,
                Constants::TBL_LANGUAGES . '.' . Constants::FLD_LANGUAGES_ID,
                '=',
                Constants::TBL_SUBMISSIONS . '.' . Constants::FLD_SUBMISSIONS_LANGUAGE_ID
            )
            ->orderByDesc(
                Constants::TBL_SUBMISSIONS . '.' . Constants::FLD_SUBMISSIONS_SUBMISSION_TIME
            );


        return $query;
    }

    /**
     * Return the basic contest query
     *
     * @param array $projection columns to select
     * @return \Illuminate\Database\Query\Builder
     */
    private function contestBasicQuery($projection)
    {
        return DB::table(Constants::TBL_CONTESTS)
            ->select($projection)
            ->where(
                Constants::TBL_CONTESTS . '.' . Constants::FLD_CONTESTS_ID,
                '=',
                DB::raw($this->id)
            );
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
        // TODO: need to check timestamps accurately
        // TODO: It seems that Codeforces timestamp is leading 4 hours
        $contestStartTime = strtotime($this->time);
        $contestEndTime = strtotime($this->time . ' + ' . $this->duration . ' minute');

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
            );
//            ->whereBetween(
//                Constants::TBL_SUBMISSIONS . '.' . Constants::FLD_SUBMISSIONS_SUBMISSION_TIME,
//                [$contestStartTime, $contestEndTime]
//            );
    }

    /**
     * Left join contest with its related submissions
     *
     * @param \Illuminate\Database\Query\Builder $query
     */
    private function contestLeftJoinSubmissions($query)
    {
        // TODO: need to check timestamps accurately
        // TODO: It seems that Codeforces timestamp is leading 4 hours
        $contestStartTime = strtotime($this->time);
        $contestEndTime = strtotime($this->time . ' + ' . $this->duration . ' minute');

        $query
            ->leftJoin(
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
            );
//            ->whereBetween(
//                Constants::TBL_SUBMISSIONS . '.' . Constants::FLD_SUBMISSIONS_SUBMISSION_TIME,
//                [$contestStartTime, $contestEndTime]
//            );
    }

    /**
     * Left join contest with its related submissions until the first accepted submission
     *
     * @param \Illuminate\Database\Query\Builder $query
     */
    private function contestLeftJoinSubmissionsUntilFirstAccepted($query)
    {
        // TODO: need to check timestamps accurately
        // TODO: It seems that Codeforces timestamp is leading 4 hours
        $contestStartTime = strtotime($this->time);
        $contestEndTime = strtotime($this->time . ' + ' . $this->duration . ' minute');

        $firstAcceptedQuery =
            DB::table(Constants::TBL_SUBMISSIONS)
                ->select(
                    Constants::TBL_SUBMISSIONS . '.' . Constants::FLD_SUBMISSIONS_SUBMISSION_TIME
                )
                ->whereColumn(
                    Constants::TBL_SUBMISSIONS . '.' . Constants::FLD_SUBMISSIONS_USER_ID,
                    '=',
                    's' . '.' . Constants::FLD_SUBMISSIONS_USER_ID
                )
                ->whereColumn(
                    Constants::TBL_SUBMISSIONS . '.' . Constants::FLD_SUBMISSIONS_PROBLEM_ID,
                    '=',
                    's' . '.' . Constants::FLD_SUBMISSIONS_PROBLEM_ID
                )
                ->where(
                    Constants::TBL_SUBMISSIONS . '.' . Constants::FLD_SUBMISSIONS_VERDICT,
                    '=',
                    DB::raw("'" . Constants::VERDICT_ACCEPTED . "'")
                )
//                ->whereBetween(
//                    Constants::TBL_SUBMISSIONS . '.' . Constants::FLD_SUBMISSIONS_SUBMISSION_TIME,
//                    [$contestStartTime, $contestEndTime]
//                )
                ->orderBy(
                    Constants::FLD_SUBMISSIONS_SUBMISSION_TIME
                )
                ->limit(1);

        $submissionsQuery =
            DB::table(Constants::TBL_SUBMISSIONS . ' as ' . 's')
                ->where(
                    's' . '.' . Constants::FLD_SUBMISSIONS_SUBMISSION_TIME,
                    '<=',
                    DB::raw('COALESCE((' . $firstAcceptedQuery->toSql() . '), UNIX_TIMESTAMP())')
                );

        $query
            ->leftJoin(
                DB::raw('(' . $submissionsQuery->toSql() . ') as ' . '`' . Constants::TBL_SUBMISSIONS . '`'),
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
            );
//            ->whereBetween(
//                Constants::TBL_SUBMISSIONS . '.' . Constants::FLD_SUBMISSIONS_SUBMISSION_TIME,
//                [$contestStartTime, $contestEndTime]
//            );
    }
}
