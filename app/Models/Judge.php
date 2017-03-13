<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;
use Illuminate\Pagination\Paginator;
use DB;
class Judge extends Model
{
    public function __construct(array $attributes = [])
    {
        $this->fillable =  [
            config('db_constants.FIELDS.FLD_JUDGES_NAME'),
            config('db_constants.FIELDS.FLD_JUDGES_LINK'),
            config('db_constants.FIELDS.FLD_JUDGES_API_LINK'),
        ];
        parent::__construct($attributes);
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

    public static function getJudgeProblems($judgeID, $page = 1)
    {
        // Set page
        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });
        // Set columns and count
        $problems = DB::table(config('db_constants.TABLES.TBL_PROBLEMS'))
            ->select(
                config('db_constants.TABLES.TBL_PROBLEMS').'.'.config('db_constants.FIELDS.FLD_PROBLEMS_ID'),
                config('db_constants.TABLES.TBL_PROBLEMS').'.'.config('db_constants.FIELDS.FLD_PROBLEMS_NAME'),
                config('db_constants.TABLES.TBL_PROBLEMS').'.'.config('db_constants.FIELDS.FLD_PROBLEMS_DIFFICULTY'),
                config('db_constants.TABLES.TBL_PROBLEMS').'.'.config('db_constants.FIELDS.FLD_PROBLEMS_ACCEPTED_SUBMISSIONS_COUNT'),
                config('db_constants.TABLES.TBL_JUDGES').'.'.config('db_constants.FIELDS.FLD_JUDGES_NAME') . ' as judge')
            ->join(config('db_constants.TABLES.TBL_JUDGES'),
                config('db_constants.TABLES.TBL_PROBLEMS').'.'. config('db_constants.FIELDS.FLD_PROBLEMS_JUDGE_ID'),
                '=',
                config('db_constants.TABLES.TBL_JUDGES').'.'. config('db_constants.FIELDS.FLD_JUDGES_ID'))
            ->where(config('db_constants.TABLES.TBL_PROBLEMS').'.'. config('db_constants.FIELDS.FLD_PROBLEMS_JUDGE_ID'), '=', $judgeID)
            ->paginate(config('constants.PROBLEMS_COUNT_PER_PAGE'));
        // Assign data
        $ret = [
            "headings" => ["ID", "Name", "Difficulty", "# Accepted submissions", "Judge"],
            "problems" => $problems,
            "extra" => [
                "checkbox" => "no",
                "checkboxPosition" => "-1",
            ]
        ];
        return json_encode($ret);
    }
}
