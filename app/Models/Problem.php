<?php

namespace App\Models;

use DB;
use Auth;
use Validator;
use App\Utilities\Constants;
use App\Exceptions\UnknownJudgeException;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use App\Utilities\Utilities;

class Problem extends Model
{
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
        Constants::FLD_PROBLEMS_DIFFICULTY,
        Constants::FLD_PROBLEMS_SOLVED_COUNT
    ];
    /**
     * The attributes that are displayable in the problems table.
     *
     * @var array
     */
    public static $displayable = [
        Constants::FLD_PROBLEMS_ID,
        Constants::FLD_PROBLEMS_NAME,
        Constants::FLD_PROBLEMS_DIFFICULTY,
        Constants::FLD_PROBLEMS_SOLVED_COUNT,
        Constants::FLD_PROBLEMS_JUDGE_NAME
    ];


    /**
     * This array contains the basic cols to be selected when getting the problems
     * @var array
     */
    private static $basicPorblemsQueryCols = [
        Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_ID,
        Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_JUDGE_FIRST_KEY,
        Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_JUDGE_SECOND_KEY,
        Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_NAME,
        Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_DIFFICULTY,
        Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_SOLVED_COUNT
    ];

    public function judge()
    {
        return $this->belongsTo(Judge::class, Constants::FLD_PROBLEMS_JUDGE_ID);
    }

    public function tags()
    {
        return $this->belongsToMany(
            Tag::class,
            Constants::TBL_PROBLEM_TAGS,
            Constants::FLD_PROBLEM_TAGS_PROBLEM_ID,
            Constants::FLD_PROBLEM_TAGS_TAG_ID
        );
    }

    public function contests()
    {
        return $this->belongsToMany(
            Contest::class,
            Constants::TBL_CONTEST_PROBLEMS,
            Constants::FLD_CONTEST_PROBLEMS_PROBLEM_ID,
            Constants::FLD_CONTEST_PROBLEMS_CONTEST_ID
        );
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, Constants::FLD_SUBMISSIONS_PROBLEM_ID);
    }

    public function store()
    {
        $v = Validator::make($this->attributes, config('rules.problem.store_validation_rules'));
        $v->validate();

        if (!$this->judge()) {
            throw new UnknownJudgeException;
        }

        $this->save();
    }

    /**
     * This function returns all the problems paginated
     * @param int $page
     * @param array $sortBy
     * @return string
     */
    public static function getAllProblems($page = 1, $sortBy = [])
    {
        $sortBy = Utilities::initializeProblemsSortByArray($sortBy);
        // Set page
        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });
        $problems = Problem::getAllProblemsForTable();
        return Utilities::prepareProblemsOutput($problems, $sortBy);
    }

    /**
     * This function returns all the raw problems query
     * (not executed yet to allow for query cascading)
     * @return $this
     */

    public static function getRawProblems()
    {
        $cols = self::$basicPorblemsQueryCols;

        // Get raw problems
        $problems = DB::table(Constants::TBL_PROBLEMS)
            ->distinct()
            ->select($cols);
        $problems->groupBy(Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_ID);
        return $problems;
    }

    /**
     * This function takes the $problems query and attach the judges to the query
     * @param $problems
     * @return mixed
     */
    public static function addJudgeDataToProblems($problems)
    {
        // The new columns to be selected
        $cols = $problems->columns;
        array_push($cols, Constants::TBL_JUDGES . '.' . Constants::FLD_JUDGES_NAME . ' as judge');
        array_push($cols, Constants::TBL_JUDGES . '.' . Constants::FLD_JUDGES_ID . ' as judge_id');

        // Select and join with judges table
        $problems
            ->select($cols)
            ->join(Constants::TBL_JUDGES,
                Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_JUDGE_ID,
                '=',
                Constants::TBL_JUDGES . '.' . Constants::FLD_JUDGES_ID);
        return $problems;
    }

    /**
     * This function takes the $problems query and attach the tags to the query
     * @param $problems
     * @return mixed
     */
    public static function addTagsDataToProblems($problems)
    {
        // Join the tags table
        $problems
            ->select($problems->columns)
            ->leftJoin(Constants::TBL_PROBLEM_TAGS . ' as  pt',
                Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_ID,
                '=',
                'pt.' . Constants::FLD_PROBLEM_TAGS_PROBLEM_ID)
            ->selectRaw('GROUP_CONCAT(DISTINCT (pt.' . Constants::FLD_PROBLEM_TAGS_TAG_ID . ')) as tags_ids');

        return $problems;
    }

    /**
     * This function takes the $problems query and attach the submissions to the query
     * @param $problems
     * @return mixed
     */
    public static function addSubmissionsDataToProblems($problems)
    {
        // If use isn't signed in this process must not execute
        if (Auth::check()) {
            // Get original columns, add the submissions verdict to them
            $cols = $problems->columns;
            array_push($cols, Constants::TBL_SUBMISSIONS . '.' . Constants::FLD_SUBMISSIONS_VERDICT);

            // Get new cols and join with submissions tbl
            $problems
                ->select($problems->columns)
                ->leftJoin(Constants::TBL_SUBMISSIONS . ' as  s', function ($join) {
                    // Join with submissions tbl on user_id = Authenticated used id
                    // && Join on problems id match condition
                    $join->on(DB::raw('s.' . Constants::FLD_SUBMISSIONS_USER_ID),
                        DB::raw('='),
                        DB::raw(Auth::user()->id));
                    $join->on(DB::raw(Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_ID),
                        DB::raw('='),
                        DB::raw('s.' . Constants::FLD_SUBMISSIONS_PROBLEM_ID));

                })
                // Merge distinct verdicts of the problem
                ->selectRaw('GROUP_CONCAT(DISTINCT (s.' . Constants::FLD_SUBMISSIONS_VERDICT . ')) as verdict');
        }
        return $problems;
    }

    /**
     * This function gets the problems for the problems table using the cascaded functions calls
     * for the problems decoration functions
     * @return problems collection
     */
    public static function getAllProblemsForTable()
    {
        return
            Problem::addSubmissionsDataToProblems(
                Problem::addTagsDataToProblems(
                    Problem::addJudgeDataToProblems(
                        Problem::getRawProblems())));
    }

    /**
     * This function applies given filters to problems set
     *
     * @param $name problem name
     * @param $tagsIDs array of tags IDs
     * @param $judgesIDs array of judges IDs
     * @param int $page
     * @param array $sortBy array of 'sort by' columns
     * @return string JSON of problems
     */
    public static function filter($name, $tagsIDs, $judgesIDs, $page = 1, $sortBy = [])
    {
        // Set up the sort by array
        $sortBy = Utilities::initializeProblemsSortByArray($sortBy);
        // Set page
        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });
        // Set columns and count
        $problems = self::getAllProblemsForTable()
            ->where(Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_NAME, 'LIKE', "%$name%");

        // If there's any judge in the judgesIDs array, add the condition
        if ($judgesIDs != [])
            $problems = $problems->whereIn(Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_JUDGE_ID, $judgesIDs);

        // If there's any tag in the tagsIDs array, join with problems_tags table, then add the condition
        if ($tagsIDs != [])
            $problems = $problems->join(Constants::TBL_PROBLEM_TAGS,
                Constants::TBL_PROBLEM_TAGS . '.' . Constants::FLD_PROBLEM_TAGS_PROBLEM_ID,
                '=',
                Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_ID)
                ->whereIn(Constants::TBL_PROBLEM_TAGS . '.' . Constants::FLD_PROBLEM_TAGS_TAG_ID, $tagsIDs);

        return Utilities::prepareProblemsOutput($problems, $sortBy);
    }

}
