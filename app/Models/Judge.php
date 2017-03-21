<?php

namespace App\Models;

use DB;
use Validator;
use App\Utilities\Constants;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use App\Utilities\Utilities;

class Judge extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Constants::TBL_JUDGES;

    /**
     * The primary key of the table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = Constants::FLD_JUDGES_ID;

    /**
     * Indicates if the primary key is auto incremented.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        Constants::FLD_JUDGES_ID,
        Constants::FLD_JUDGES_NAME,
        Constants::FLD_JUDGES_LINK
    ];

    /**
     * Return all problems of the current online judge
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function problems()
    {
        return $this->hasMany(Problem::class, Constants::FLD_PROBLEMS_JUDGE_ID);
    }

    /**
     * Return all submissions on the current online judge
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function submissions()
    {
        return $this->hasManyThrough(
            Submission::class,
            Problem::class,
            Constants::FLD_PROBLEMS_JUDGE_ID,
            Constants::FLD_SUBMISSIONS_PROBLEM_ID,
            Constants::FLD_JUDGES_ID
        );
    }

    /**
     * Return all users who have handles on the current online judge
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            Constants::TBL_USER_HANDLES,
            Constants::FLD_USER_HANDLES_JUDGE_ID,
            Constants::FLD_USER_HANDLES_USER_ID
        )->withPivot(Constants::FLD_USER_HANDLES_HANDLE);
    }

    /**
     * Return user model corresponding to the given handle, if not found then null is returned
     *
     * @param string $handle
     * @return User|null
     */
    public function user($handle)
    {
        return $this->users()->wherePivot(Constants::FLD_USER_HANDLES_HANDLE, $handle)->first();
    }

    public function store()
    {
        $v = Validator::make($this->attributes, config('rules.judge.store_validation_rules'));
        $v->validate();
        $this->save();
    }

    public static function getAllJudges()
    {
        return json_encode(
            DB::table(Constants::TBL_JUDGES)
                ->select(
                    Constants::FLD_JUDGES_ID,
                    Constants::FLD_JUDGES_NAME
                )
                ->get()
        );
    }

    public static function getJudgeProblems($judgeID, $page = 1, $sortBy = [])
    {
        $sortBy = Utilities::initializeProblemsSortByArray($sortBy);

        // Set page
        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });
        $problems = Problem::getAllProblemsForTable();
        $problems = $problems->where(Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_JUDGE_ID, '=', $judgeID);
        return Utilities::prepareProblemsOutput($problems, $sortBy);
    }
}
