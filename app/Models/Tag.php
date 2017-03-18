<?php

namespace App\Models;

use DB;
use Validator;
use App\Utilities\Constants;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Constants::TBL_TAGS;

    /**
     * The primary key of the table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = Constants::FLD_TAGS_ID;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        Constants::FLD_TAGS_NAME
    ];

    public function problems()
    {
        return $this->belongsToMany(
            Problem::class,
            Constants::TBL_PROBLEM_TAGS,
            Constants::FLD_PROBLEM_TAGS_TAG_ID,
            Constants::FLD_PROBLEM_TAGS_PROBLEM_ID
        );
    }

    public function store()
    {
        $v = Validator::make($this->attributes, config('rules.tag.store_validation_rules'));
        $v->validate();
        $this->save();
    }

    public static function index()
    {
        return json_encode(
            DB::table(Constants::TBL_TAGS)
                ->select(
                    Constants::FLD_TAGS_ID,
                    Constants::FLD_TAGS_NAME
                )
                ->get()
        );
    }

    public static function getTagProblems($tagID, $page = 1, $sortBy = [])
    {
        $sortBy = Problem::initializeProblemsSortByArray($sortBy);
        // Set page
        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });
        $problems = Problem::prepareBasicProblemsCollection();
        $problems = $problems->join(Constants::TBL_PROBLEM_TAGS,
                Constants::TBL_PROBLEM_TAGS . '.' . Constants::FLD_PROBLEM_TAGS_PROBLEM_ID,
                '=',
                Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_ID)
            ->where(Constants::TBL_PROBLEM_TAGS . '.' . Constants::FLD_PROBLEM_TAGS_TAG_ID, '=', $tagID);
        return Problem::prepareProblemsOutput($problems, $sortBy);
    }
}
