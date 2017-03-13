<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;
use DB;
use Illuminate\Pagination\Paginator;

class Tag extends Model
{
    public function __construct(array $attributes = [])
    {
        $this->fillable = [
            config('db_constants.FIELDS.FLD_TAGS_NAME'),
        ];
        parent::__construct($attributes);
    }

    public function problems()
    {
        return $this->belongsToMany(Problem::class, config('db_constants.TABLES.TBL_PROBLEM_TAG'));
    }

    public function store()
    {
        $v = Validator::make($this->attributes, config('rules.tag.store_validation_rules'));
        $v->validate();
        $this->save();
    }

    public static function index($count = 15)
    {
        return json_encode(
            DB::table(config('db_constants.TABLES.TBL_TAGS'))
                ->select(
                    config('db_constants.FIELDS.FLD_TAGS_ID'),
                    config('db_constants.FIELDS.FLD_TAGS_NAME'))
                ->take($count)->get());
    }

    public static function getTagProblems($tagID, $page = 1)
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
            ->join(config('db_constants.TABLES.TBL_PROBLEM_TAG'),
                config('db_constants.TABLES.TBL_PROBLEM_TAG').'.'. config('db_constants.FIELDS.FLD_PROBLEM_TAG_PROBLEM_ID'),
                '=',
                config('db_constants.TABLES.TBL_PROBLEMS').'.'. config('db_constants.FIELDS.FLD_PROBLEMS_ID'))
            ->where(config('db_constants.TABLES.TBL_PROBLEM_TAG').'.'. config('db_constants.FIELDS.FLD_PROBLEM_TAG_TAG_ID'), '=', $tagID)
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
