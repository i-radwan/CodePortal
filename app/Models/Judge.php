<?php

namespace App\Models;

use DB;
use Validator;
use App\Utilities\Constants;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;

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
    //public $incrementing = false;

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
        Constants::FLD_JUDGES_NAME,
        Constants::FLD_JUDGES_LINK
    ];

    public function problems()
    {
        return $this->hasMany(Problem::class, Constants::FLD_PROBLEMS_JUDGE_ID);
    }

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

    public function store()
    {
        $v = Validator::make($this->attributes, config('rules.judge.store_validation_rules'));
        $v->validate();
        $this->save();
    }

    public static function index()
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
        $sortBy = Problem::initializeProblemsSortByArray($sortBy);

        // Set page
        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });
        $problems = Problem::prepareBasicProblemsCollection();
        $problems = $problems->where(Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_JUDGE_ID, '=', $judgeID);
        return Problem::prepareProblemsOutput($problems, $sortBy);
    }
}
