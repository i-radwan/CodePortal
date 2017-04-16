<?php

namespace App\Models;

use App\Utilities\Constants;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Problem extends Model
{
    use ValidateModelData;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Constants::TBL_PROBLEMS;

    /**
     * The primary key of the table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = Constants::FLD_PROBLEMS_ID;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        Constants::FLD_PROBLEMS_NAME,
        Constants::FLD_PROBLEMS_JUDGE_FIRST_KEY,
        Constants::FLD_PROBLEMS_JUDGE_SECOND_KEY,
        Constants::FLD_PROBLEMS_SOLVED_COUNT
    ];

    /**
     * The rules to check against before saving the model
     *
     * @var array
     */
    protected $rules = [
        // TODO: validating super unique key
        Constants::FLD_PROBLEMS_NAME => 'required|max:255',
        Constants::FLD_PROBLEMS_JUDGE_ID => 'integer|required|exists:' . Constants::TBL_JUDGES . ',' . Constants::FLD_JUDGES_ID,
        Constants::FLD_PROBLEMS_JUDGE_ID => 'unique_with:' . Constants::TBL_PROBLEMS . ',' . Constants::FLD_PROBLEMS_JUDGE_FIRST_KEY . ',' . Constants::FLD_PROBLEMS_JUDGE_SECOND_KEY,
        Constants::FLD_PROBLEMS_JUDGE_FIRST_KEY => 'required|min:0',
        Constants::FLD_PROBLEMS_JUDGE_SECOND_KEY => 'required|max:10',
        Constants::FLD_PROBLEMS_SOLVED_COUNT => 'integer|required|min:0'
    ];

    /**
     * The basic database columns to be selected when getting the problems
     *
     * @var array
     */
    private static $basicProblemsQueryCols = [
        Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_ID,
        Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_JUDGE_ID,
        Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_JUDGE_FIRST_KEY,
        Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_JUDGE_SECOND_KEY,
        Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_NAME,
        Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_SOLVED_COUNT
    ];

    /**
     * Return the hosting online judge of the current problem
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function judge()
    {
        return $this->belongsTo(Judge::class, Constants::FLD_PROBLEMS_JUDGE_ID);
    }

    /**
     * Return the tags of the current problem
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(
            Tag::class,
            Constants::TBL_PROBLEM_TAGS,
            Constants::FLD_PROBLEM_TAGS_PROBLEM_ID,
            Constants::FLD_PROBLEM_TAGS_TAG_ID
        );
    }

    /**
     * Return the contests having the current problem as one of their problems list
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function contests()
    {
        return $this->belongsToMany(
            Contest::class,
            Constants::TBL_CONTEST_PROBLEMS,
            Constants::FLD_CONTEST_PROBLEMS_PROBLEM_ID,
            Constants::FLD_CONTEST_PROBLEMS_CONTEST_ID
        );
    }

    /**
     * Return the submissions current problem
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function submissions()
    {
        return $this->hasMany(Submission::class, Constants::FLD_SUBMISSIONS_PROBLEM_ID);
    }

    /**
     * Return the simple verdict of the current problem for the given user,
     * if no user is passed then not solved verdict is returned
     *
     * @param User|null $user
     * @return int the simple verdict of the problem: 0 not solved, 1 accepted, 2 wrong submission
     */
    public function simpleVerdict(User $user = null)
    {
        if ($user == null) {
            return Constants::SIMPLE_VERDICT_NOT_SOLVED;
        }

        // Count the number of accepted submissions
        $acceptedSubmissions = $this
            ->submissions()
            ->where([
                [Constants::FLD_SUBMISSIONS_USER_ID, '=', $user->id],
                [Constants::FLD_SUBMISSIONS_VERDICT, '=', Constants::VERDICT_ACCEPTED]
            ])
            ->count();

        if ($acceptedSubmissions > 0) {
            return Constants::SIMPLE_VERDICT_ACCEPTED;
        }

        // Count the total number of submissions
        $submissions = $this
            ->submissions()
            ->where(Constants::FLD_SUBMISSIONS_USER_ID, '=', $user->id)
            ->count();

        if ($submissions > 0) {
            return Constants::SIMPLE_VERDICT_WRONG_SUBMISSION;
        }

        return Constants::SIMPLE_VERDICT_NOT_SOLVED;
    }

    /**
     * Scope a query to only include problems with the given name
     *
     * @param Builder $query
     * @param string|null $name
     * @return Builder
     */
    public function scopeOfName(Builder $query, $name = null)
    {
        $query->select(self::$basicProblemsQueryCols);

        if ($name == null || $name == "") {
            return $query;
        }

        $query->where(
            Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_NAME,
            'LIKE',
            "%$name%"
        );

        return $query;
    }

    /**
     * Scope a query to only include problems that belong to one of the given judges
     *
     * @param Builder $query
     * @param array|null $judgesIDs
     * @return Builder
     */
    public function scopeOfJudges(Builder $query, $judgesIDs = null)
    {
        if ($judgesIDs == null || $judgesIDs == []) {
            return $query;
        }

        $query->whereIn(
            Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_JUDGE_ID,
            $judgesIDs
        );

        return $query;
    }

    /**
     * Scope a query to only include problems having one or more of the given tags
     *
     * @param Builder $query
     * @param array|null $tagsIDs
     * @return Builder
     */
    public function scopeHasTags(Builder $query, $tagsIDs = null)
    {
        if ($tagsIDs == null || $tagsIDs == []) {
            return $query;
        }

        $query
            ->distinct()
            ->join(
                Constants::TBL_PROBLEM_TAGS,
                Constants::TBL_PROBLEM_TAGS . '.' . Constants::FLD_PROBLEM_TAGS_PROBLEM_ID,
                '=',
                Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_ID
            )
            ->whereIn(
                Constants::TBL_PROBLEM_TAGS . '.' . Constants::FLD_PROBLEM_TAGS_TAG_ID,
                $tagsIDs
            );

        return $query;
    }
}
