<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\UnknownJudgeException;
use Validator;
use Illuminate\Pagination\Paginator;
use DB;

class Problem extends Model
{
    public function __construct(array $attributes = [])
    {
        $this->fillable = [
            config('db_constants.FIELDS.FLD_PROBLEMS_NAME'),
            config('db_constants.FIELDS.FLD_PROBLEMS_DIFFICULTY'),
            config('db_constants.FIELDS.FLD_PROBLEMS_ACCEPTED_SUBMISSIONS_COUNT')
        ];
        parent::__construct($attributes);
    }

    public function contests()
    {
        return $this->belongsToMany(Contest::class, config('db_constants.TABLES.TBL_CONTEST_PROBLEM'));
    }

    public function judge()
    {
        return $this->belongsTo(Judge::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, config('db_constants.TABLES.TBL_PROBLEM_TAG'));
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
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

    public static function index($page = 1)
    {
        // Set page
        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });
        // Set columns and count
        $problems = DB::table(config('db_constants.TABLES.TBL_PROBLEMS'))
            ->select(
                config('db_constants.TABLES.TBL_PROBLEMS') . '.' . config('db_constants.FIELDS.FLD_PROBLEMS_ID'),
                config('db_constants.TABLES.TBL_PROBLEMS') . '.' . config('db_constants.FIELDS.FLD_PROBLEMS_NAME'),
                config('db_constants.TABLES.TBL_PROBLEMS') . '.' . config('db_constants.FIELDS.FLD_PROBLEMS_DIFFICULTY'),
                config('db_constants.TABLES.TBL_PROBLEMS') . '.' . config('db_constants.FIELDS.FLD_PROBLEMS_ACCEPTED_SUBMISSIONS_COUNT'),
                config('db_constants.TABLES.TBL_JUDGES') . '.' . config('db_constants.FIELDS.FLD_JUDGES_NAME') . ' as judge')
            ->join(config('db_constants.TABLES.TBL_JUDGES'),
                config('db_constants.TABLES.TBL_PROBLEMS') . '.' . config('db_constants.FIELDS.FLD_PROBLEMS_JUDGE_ID'),
                '=',
                config('db_constants.TABLES.TBL_JUDGES') . '.' . config('db_constants.FIELDS.FLD_JUDGES_ID'))
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

    /**
     * This function applies given filters to problems set
     * @param $name problem name
     * @param $tagsIDs array of tags IDs
     * @param $judgesIDs array of judges IDs
     * @param int $page
     * @return string JSON of problems
     */
    public static function filter($name, $tagsIDs, $judgesIDs, $page = 1)
    {
        // Set page
        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });
        DB::enableQueryLog();
        // Set columns and count
        $problems = DB::table(config('db_constants.TABLES.TBL_PROBLEMS'))
            ->select(
                config('db_constants.TABLES.TBL_PROBLEMS') . '.' . config('db_constants.FIELDS.FLD_PROBLEMS_ID'),
                config('db_constants.TABLES.TBL_PROBLEMS') . '.' . config('db_constants.FIELDS.FLD_PROBLEMS_NAME'),
                config('db_constants.TABLES.TBL_PROBLEMS') . '.' . config('db_constants.FIELDS.FLD_PROBLEMS_DIFFICULTY'),
                config('db_constants.TABLES.TBL_PROBLEMS') . '.' . config('db_constants.FIELDS.FLD_PROBLEMS_ACCEPTED_SUBMISSIONS_COUNT'),
                config('db_constants.TABLES.TBL_JUDGES') . '.' . config('db_constants.FIELDS.FLD_JUDGES_NAME') . ' as judge')
            ->join(config('db_constants.TABLES.TBL_JUDGES'),
                config('db_constants.TABLES.TBL_PROBLEMS') . '.' . config('db_constants.FIELDS.FLD_PROBLEMS_JUDGE_ID'),
                '=',
                config('db_constants.TABLES.TBL_JUDGES') . '.' . config('db_constants.FIELDS.FLD_JUDGES_ID'))
            ->join(config('db_constants.TABLES.TBL_PROBLEM_TAG'),
                config('db_constants.TABLES.TBL_PROBLEM_TAG') . '.' . config('db_constants.FIELDS.FLD_PROBLEM_TAG_PROBLEM_ID'),
                '=',
                config('db_constants.TABLES.TBL_PROBLEMS') . '.' . config('db_constants.FIELDS.FLD_PROBLEMS_ID'))
            ->whereIn(config('db_constants.TABLES.TBL_PROBLEM_TAG') . '.' . config('db_constants.FIELDS.FLD_PROBLEM_TAG_TAG_ID'), $tagsIDs)
            ->whereIn(config('db_constants.TABLES.TBL_PROBLEMS') . '.' . config('db_constants.FIELDS.FLD_PROBLEMS_JUDGE_ID'), $judgesIDs)
            ->where(config('db_constants.TABLES.TBL_PROBLEMS') . '.' . config('db_constants.FIELDS.FLD_PROBLEMS_NAME'), 'LIKE', "%$name%")
            ->orderBy(config('db_constants.TABLES.TBL_PROBLEMS') . '.' . config('db_constants.FIELDS.FLD_PROBLEMS_ID'))
            ->paginate(config('constants.PROBLEMS_COUNT_PER_PAGE')*10);
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
