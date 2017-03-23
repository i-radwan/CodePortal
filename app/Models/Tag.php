<?php

namespace App\Models;

use App\Utilities\Constants;
use App\Utilities\Utilities;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use ValidateModelData;

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

    /**
     * The rules to check against before saving the model
     *
     * @var array
     */
    protected $rules = [
        Constants::FLD_TAGS_NAME => 'required|unique:' . Constants::TBL_TAGS . '|max:50'
    ];

    /**
     * Return all problems having the current tag as one of their tags
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function problems()
    {
        return $this->belongsToMany(
            Problem::class,
            Constants::TBL_PROBLEM_TAGS,
            Constants::FLD_PROBLEM_TAGS_TAG_ID,
            Constants::FLD_PROBLEM_TAGS_PROBLEM_ID
        );
    }

    public static function getTagProblems($tagID, $page = 1, $sortBy = [])
    {
        $sortBy = Utilities::initializeProblemsSortByArray($sortBy);
        // Set page
        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });
        // Get all problems
        $problems = Problem::getAllProblemsForTable();

        // Join with problems tags
        $problems = $problems->join(Constants::TBL_PROBLEM_TAGS,
                Constants::TBL_PROBLEM_TAGS . '.' . Constants::FLD_PROBLEM_TAGS_PROBLEM_ID,
                '=',
                Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_ID)
            ->where(Constants::TBL_PROBLEM_TAGS . '.' . Constants::FLD_PROBLEM_TAGS_TAG_ID, '=', $tagID);
        return Utilities::prepareProblemsOutput($problems, $sortBy);
    }
}
