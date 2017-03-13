<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;
use DB;
use Illuminate\Pagination\Paginator;
use App\Utilities\Constants;

class Tag extends Model
{
    public function __construct(array $attributes = [])
    {
        $this->fillable = [
            Constants::FLD_TAGS_NAME,
        ];
        parent::__construct($attributes);
    }

    public function problems()
    {
        return $this->belongsToMany(Problem::class, Constants::TBL_PROBLEM_TAG);
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
            DB::table(Constants::TBL_TAGS)
                ->select(
                    Constants::FLD_TAGS_ID,
                    Constants::FLD_TAGS_NAME)
                ->take($count)->get());
    }

    public static function getTagProblems($tagID, $page = 1, $sortBy = [])
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
            ->join(Constants::TBL_PROBLEM_TAG,
                Constants::TBL_PROBLEM_TAG . '.' . Constants::FLD_PROBLEM_TAG_PROBLEM_ID,
                '=',
                Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_ID)
            ->where(Constants::TBL_PROBLEM_TAG . '.' . Constants::FLD_PROBLEM_TAG_TAG_ID, '=', $tagID);
        return Problem::prepareProblemsOutput($problems, $sortBy);
    }
}
