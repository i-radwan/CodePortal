<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\UnknownJudgeException;
use Validator;
use DB;

class Problem extends Model
{
    protected $fillable = ['name', 'difficulty', 'accepted_submissions_count'];

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

    public static function index()
    {
        $ret = [
            "headings" => ["ID", "Name", "Difficulty", "# Accepted submissions", "Judge"],
            "problems" => Problem::paginate(config('constants.PROBLEMS_COUNT_PER_PAGE')),
            "extra" => [
                "checkbox" => "no",
                "checkboxPosition" => "-1",
            ]
        ];
        return json_encode($ret);
    }
}
