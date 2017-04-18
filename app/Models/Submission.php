<?php
namespace App\Models;
use DB;
use App\Utilities\Constants;
use Illuminate\Database\Eloquent\Model;
class Submission extends Model
{
    use ValidateModelData;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Constants::TBL_SUBMISSIONS;
    /**
     * The primary key of the table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = Constants::FLD_SUBMISSIONS_ID;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        Constants::FLD_SUBMISSIONS_USER_ID,
        Constants::FLD_SUBMISSIONS_PROBLEM_ID,
        Constants::FLD_SUBMISSIONS_JUDGE_SUBMISSION_ID,
        Constants::FLD_SUBMISSIONS_LANGUAGE_ID,
        Constants::FLD_SUBMISSIONS_SUBMISSION_TIME,
        Constants::FLD_SUBMISSIONS_EXECUTION_TIME,
        Constants::FLD_SUBMISSIONS_CONSUMED_MEMORY,
        Constants::FLD_SUBMISSIONS_VERDICT
    ];
    /**
     * The rules to check against before saving the model
     *
     * @var array
     */
    protected $rules = [
        Constants::FLD_SUBMISSIONS_USER_ID => 'required|integer|exists:' . Constants::TBL_USERS . ',' . Constants::FLD_USERS_ID,
        Constants::FLD_SUBMISSIONS_PROBLEM_ID => 'required|integer|exists:' . Constants::TBL_PROBLEMS . ',' . Constants::FLD_PROBLEMS_ID,
        Constants::FLD_SUBMISSIONS_JUDGE_SUBMISSION_ID => 'required|integer|unique:' . Constants::TBL_SUBMISSIONS,
        Constants::FLD_SUBMISSIONS_LANGUAGE_ID => 'required|integer|exists:' . Constants::TBL_LANGUAGES . ',' . Constants::FLD_LANGUAGES_ID,
        Constants::FLD_SUBMISSIONS_SUBMISSION_TIME => 'required|integer|min:0',
        Constants::FLD_SUBMISSIONS_EXECUTION_TIME => 'required|integer|min:0',
        Constants::FLD_SUBMISSIONS_CONSUMED_MEMORY => 'required|integer|min:0',
        Constants::FLD_SUBMISSIONS_VERDICT => 'integer|required|min:0|max:' . Constants::VERDICT_COUNT
    ];
    /**
     * Return the owner user of the current submission
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, Constants::FLD_SUBMISSIONS_USER_ID);
    }
    /**
     * Return the problem related to the current submission
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function problem()
    {
        return $this->belongsTo(Problem::class, Constants::FLD_SUBMISSIONS_PROBLEM_ID);
    }
    /**
     * Return the programming language of the current submission
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function language()
    {
        return $this->belongsTo(Language::class, Constants::FLD_SUBMISSIONS_LANGUAGE_ID);
    }
    /**
     * Return all submissions between the given interval of time
     * until the first accepted submission for a user per problem
     *
     * @param int $startTime
     * @param int $endTime
     * @return \Illuminate\Database\Query\Builder
     */
    public static function tillFirstAccepted($startTime, $endTime)
    {
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
                    Constants::VERDICT_ACCEPTED
                )
                ->whereBetween(
                    Constants::TBL_SUBMISSIONS . '.' . Constants::FLD_SUBMISSIONS_SUBMISSION_TIME,
                    [$startTime, $endTime]
                )
                ->orderBy(
                    Constants::FLD_SUBMISSIONS_SUBMISSION_TIME
                )
                ->limit(1);
        $query =
            DB::table(Constants::TBL_SUBMISSIONS . ' as ' . 's')
                ->where(
                    's' . '.' . Constants::FLD_SUBMISSIONS_SUBMISSION_TIME,
                    '>=',
                    $startTime
                )
                ->where(
                    's' . '.' . Constants::FLD_SUBMISSIONS_SUBMISSION_TIME,
                    '<=',
                    DB::raw('COALESCE((' . $firstAcceptedQuery->toSql() . '), ' . $endTime .  ')')
                )
                ->mergeBindings($firstAcceptedQuery);
        return $query;
    }
}