<?php

namespace App\Models;

use DB;
use Validator;
use App\Utilities\Constants;
use App\Exceptions\UnknownJudgeException;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;

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
        Constants::FLD_PROBLEMS_ACCEPTED_SUBMISSIONS_COUNT
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

    public static function index($page = 1, $sortBy = [])
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
                Constants::TBL_JUDGES . '.' . Constants::FLD_JUDGES_ID);
        return Problem::prepareProblemsOutput($problems, $sortBy);
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
            ->join(Constants::TBL_PROBLEM_TAGS,
                Constants::TBL_PROBLEM_TAGS . '.' . Constants::FLD_PROBLEM_TAGS_PROBLEM_ID,
                '=',
                Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_ID)
            ->whereIn(Constants::TBL_PROBLEM_TAGS . '.' . Constants::FLD_PROBLEM_TAGS_TAG_ID, $tagsIDs)
            ->whereIn(Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_JUDGE_ID, $judgesIDs)
            ->where(Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_NAME, 'LIKE', "%$name%");

        return Problem::prepareProblemsOutput($problems, $sortBy);
    }

    /**
     * This function takes the given sortBy array and if it's empty it generates the
     * basic sort by condition
     *
     * @param $sortBy
     * @return array
     */
    public static function initializeProblemsSortByArray($sortBy)
    {
        if (count($sortBy) == 0) {
            $sortBy = [
                [
                    "column" => Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_ID,
                    "mode" => "asc"
                ]
            ];
        }
        return $sortBy;
    }

    /**
     * This function applies the sortBy array to the problems collection and paginate it
     * then it adds the extra info like headings and return the json encoded string
     *
     * @param $problems
     * @param $sortBy
     * @return string
     */
    public static function prepareProblemsOutput($problems, $sortBy)
    {
        // Apply sorting
        foreach ($sortBy as $sortField) {
            $problems->orderBy($sortField["column"], $sortField["mode"]);
        }
        // Paginate
        $problems = $problems->paginate(Constants::PROBLEMS_COUNT_PER_PAGE);
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
