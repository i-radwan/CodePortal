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
        Constants::FLD_CONTESTS_VISIBILITY,
        Constants::FLD_CONTESTS_OWNER_ID
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
     * The basic database columns to be selected when getting the contest statistics per user
     *
     * @var array
     */
    protected $basicStandingsUsersQueryCols = [
        Constants::TBL_USERS . '.' . Constants::FLD_USERS_ID . ' as ' . Constants::FLD_SUBMISSIONS_USER_ID,
        Constants::TBL_USERS . '.' . Constants::FLD_USERS_USERNAME
    ];

    /**
     * The basic database columns to be selected when getting the contest statistics per problem per user
     *
     * @var array
     */
    protected $basicStandingsUsersProblemsQueryCols = [
        Constants::TBL_USERS . '.' . Constants::FLD_USERS_ID . ' as ' . Constants::FLD_SUBMISSIONS_USER_ID,
        Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_ID . ' as ' . Constants::FLD_SUBMISSIONS_PROBLEM_ID,
        Constants::TBL_CONTEST_PROBLEMS . '.' . Constants::FLD_CONTEST_PROBLEMS_PROBLEM_ORDER
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
        Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_JUDGE_FIRST_KEY,
        Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_JUDGE_SECOND_KEY,
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
        )->withPivot(Constants::FLD_CONTEST_PROBLEMS_PROBLEM_ORDER);
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
     * Return all participating teams of the current contest
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function participantTeams()
    {
        return $this->belongsToMany(
            Team::class,
            Constants::TBL_CONTEST_TEAMS,
            Constants::FLD_CONTEST_TEAMS_CONTEST_ID,
            Constants::FLD_CONTEST_TEAMS_TEAM_ID
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
     * Return all groups related to this contest
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups()
    {
        return $this->belongsToMany(
            Contest::class,
            Constants::TBL_GROUPS_CONTESTS,
            Constants::FLD_GROUP_CONTESTS_CONTEST_ID,
            Constants::FLD_GROUP_CONTESTS_GROUP_ID
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
     * Return the problems of the current contest along with
     * the total number of submissions and the number of accepted submissions per problem
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function problemStatistics()
    {
        $query = $this->contestBasicQuery($this->basicContestProblemsQueryCols, true);
        $this->countAcceptedSubmissionsQuery($query, Constants::FLD_PROBLEMS_SOLVED_COUNT);
        $this->countSubmissionsQuery($query, Constants::FLD_PROBLEMS_TRAILS_COUNT);
        $query->groupBy(Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_ID);
        $query->orderBy(Constants::TBL_CONTEST_PROBLEMS . '.' . Constants::FLD_CONTEST_PROBLEMS_PROBLEM_ORDER);
        return $query;
    }

    /**
     * Return the standings of the current contest
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function standings()
    {
        // Statistics per user
        $t1 = $this->contestBasicQuery($this->basicStandingsUsersQueryCols, true);
        $this->countAcceptedSubmissionsQuery($t1, Constants::FLD_USERS_SOLVED_COUNT);
        $this->countSubmissionsQuery($t1, Constants::FLD_USERS_TRAILS_COUNT);
        $this->calculateUsersPenalty($t1, Constants::FLD_USERS_PENALTY);
        $t1->groupBy(Constants::TBL_USERS . '.' . Constants::FLD_USERS_ID);

        // Statistics per problem per user
        $t2 = $this->contestBasicQuery($this->basicStandingsUsersProblemsQueryCols, true);
        $this->countAcceptedSubmissionsQuery($t2, Constants::FLD_PROBLEMS_SOLVED_COUNT);
        $this->countSubmissionsQuery($t2, Constants::FLD_PROBLEMS_TRAILS_COUNT);
        $t2->groupBy(Constants::TBL_USERS . '.' . Constants::FLD_USERS_ID);
        $t2->groupBy(Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_ID);

        // Join the two tables
        $query = DB::table(DB::raw(
            "(" . $t1->toSql() . ") as `t1` natural join (" . $t2->toSql() . ") as `t2`"
        ));

        $query->mergeBindings($t1)->mergeBindings($t2);

        // Sort the standings
        $query->orderByDesc(Constants::FLD_USERS_SOLVED_COUNT);
        $query->orderBy(Constants::FLD_USERS_PENALTY);
        $query->orderBy(Constants::FLD_USERS_TRAILS_COUNT);
        $query->orderBy(Constants::FLD_SUBMISSIONS_USER_ID);
        $query->orderBy(Constants::FLD_CONTEST_PROBLEMS_PROBLEM_ORDER);

        return $query;
    }

    /**
     * Return all submissions of the current contest
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function submissions()
    {
        $query = $this->contestBasicQuery($this->basicContestSubmissionsQueryCols, false);

        // Join with language table
        $query->join(
            Constants::TBL_LANGUAGES,
            Constants::TBL_LANGUAGES . '.' . Constants::FLD_LANGUAGES_ID,
            '=',
            Constants::TBL_SUBMISSIONS . '.' . Constants::FLD_SUBMISSIONS_LANGUAGE_ID
        );

        $query->orderByDesc(Constants::TBL_SUBMISSIONS . '.' . Constants::FLD_SUBMISSIONS_SUBMISSION_TIME);
        return $query;
    }

    /**
     * Return the basic contest query that joins
     * the contest with problems, users and submissions
     *
     * @param array $projections Columns to select
     * @param bool $tillFirstAccepted Whether to join with all submission or with submissions until first accepted
     * @return \Illuminate\Database\Query\Builder
     */
    private function contestBasicQuery($projections, $tillFirstAccepted = false)
    {
        $query = DB::table(Constants::TBL_CONTESTS)->select($projections);

        $this->contestJoinProblems($query);
        $this->contestJoinUsers($query);
        $this->contestJoinSubmissions($query, $tillFirstAccepted);

        // Note that this where clause should come after the joining for correct binding
        $query->where(
            Constants::TBL_CONTESTS . '.' . Constants::FLD_CONTESTS_ID,
            '=',
            $this->id
        );

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
            ->leftJoin(
                Constants::TBL_CONTEST_PROBLEMS,
                Constants::TBL_CONTEST_PROBLEMS . '.' . Constants::FLD_CONTEST_PROBLEMS_CONTEST_ID,
                '=',
                Constants::TBL_CONTESTS . '.' . Constants::FLD_CONTESTS_ID
            )
            ->leftJoin(
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
            ->leftJoin(
                Constants::TBL_CONTEST_PARTICIPANTS,
                Constants::TBL_CONTEST_PARTICIPANTS . '.' . Constants::FLD_CONTEST_PARTICIPANTS_CONTEST_ID,
                '=',
                Constants::TBL_CONTESTS . '.' . Constants::FLD_CONTESTS_ID
            )
            ->leftJoin(
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
     * @param bool $tillFirstAccepted Whether to join with all submission or with submissions until first accepted
     */
    private function contestJoinSubmissions($query, $tillFirstAccepted = false)
    {
        // TODO: need to check timestamps accurately
        // TODO: It seems that Codeforces timestamp is leading 4 hours
        $contestStartTime = strtotime($this->time);
        $contestEndTime = strtotime($this->time . ' + ' . $this->duration . ' minute');

        if ($tillFirstAccepted) {
            $submissions = Submission::tillFirstAccepted($contestStartTime, $contestEndTime);
            $submissionsTable = DB::raw('(' . $submissions->toSql() . ') as ' . '`' . Constants::TBL_SUBMISSIONS . '`');
            $query->mergeBindings($submissions);
        }
        else {
            $submissionsTable = Constants::TBL_SUBMISSIONS;
            $query->whereBetween(
                Constants::TBL_SUBMISSIONS . '.' . Constants::FLD_SUBMISSIONS_SUBMISSION_TIME,
                [$contestStartTime, $contestEndTime]
            );
        }

        $query
            ->leftJoin(
                $submissionsTable,
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
    }

    /**
     * Count the total number of submissions in the given contest query
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $columnAlias
     */
    private function countSubmissionsQuery($query, $columnAlias)
    {
        $query->addSelect(DB::raw(
            "sum(case when " .
            "`" . Constants::TBL_SUBMISSIONS . "`.`" . Constants::FLD_SUBMISSIONS_VERDICT . "` " .
            "is not null " .
            "then 1 else 0 end) as " .
            "`" . $columnAlias . "`"
        ));
    }

    /**
     * Count the number of accepted submissions in the given contest query
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $columnAlias
     */
    private function countAcceptedSubmissionsQuery($query, $columnAlias)
    {
        $query->addSelect(DB::raw(
            "sum(case when " .
            "`" . Constants::TBL_SUBMISSIONS . "`.`" . Constants::FLD_SUBMISSIONS_VERDICT . "` " .
            "= " .
            "'" . Constants::VERDICT_ACCEPTED . "' " .
            "then 1 else 0 end) as " .
            "`" . $columnAlias . "`"
        ));
    }

    /**
     * Calculate the penalty of the users in the given contest query
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $columnAlias
     */
    private function calculateUsersPenalty($query, $columnAlias)
    {
        $query->addSelect(DB::raw(
            "sum(case when " .
            "`" . Constants::TBL_SUBMISSIONS . "`.`" . Constants::FLD_SUBMISSIONS_VERDICT . "` " .
            "= " .
            "'" . Constants::VERDICT_ACCEPTED . "' " .
            "then " .
            "`" . Constants::TBL_SUBMISSIONS . "`.`" . Constants::FLD_SUBMISSIONS_SUBMISSION_TIME . "` " .
            "- " .
            "UNIX_TIMESTAMP(" .
            "`" . Constants::TBL_CONTESTS . "`.`" . Constants::FLD_CONTESTS_TIME . "`" .
            ") " .
            "else 0 end) as " .
            "`" . $columnAlias . "`"
        ));
    }
}
