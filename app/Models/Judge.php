<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;
use Illuminate\Pagination\Paginator;
use DB;
use App\Utilities\Constants;

class Judge extends Model
{
    public function __construct(array $attributes = [])
    {
        $this->fillable = [
            Constants::FLD_JUDGES_NAME,
            Constants::FLD_JUDGES_LINK,
            Constants::FLD_JUDGES_API_LINK,
        ];
        parent::__construct($attributes);
    }

    public static function index()
    {
        return json_encode(
            DB::table(Constants::TBL_JUDGES)
                ->select(
                    Constants::FLD_JUDGES_ID,
                    Constants::FLD_JUDGES_NAME)
                ->get());
    }

    public function problems()
    {
        return $this->hasMany(Problem::class);
    }

    public function store()
    {
        $v = Validator::make($this->attributes, config('rules.judge.store_validation_rules'));
        $v->validate();
        $this->save();
    }

    public static function getJudgeProblems($judgeID, $page = 1, $sortBy = [])
    {
        $sortBy = Problem::initializeProblemsSortByArray($sortBy);

        // Set page
        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });
        // Set columns and count
        $problems = DB::table(Constants::TBL_PROBLEMS)
            ->select(
                Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_ID,
                Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_NAME,
                Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_DIFFICULTY,
                Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_ACCEPTED_SUBMISSIONS_COUNT,
                Constants::TBL_JUDGES . '.' . Constants::FLD_JUDGES_NAME . ' as judge')
            ->join(Constants::TBL_JUDGES,
                Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_JUDGE_ID,
                '=',
                Constants::TBL_JUDGES . '.' . Constants::FLD_JUDGES_ID)
            ->where(Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_JUDGE_ID, '=', $judgeID);
        return Problem::prepareProblemsOutput($problems, $sortBy);
    }
}
