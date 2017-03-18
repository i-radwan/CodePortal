<?php

namespace App\Models;

use Validator;
use App\Utilities\Constants;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Constants::TBL_SUBMISSIONS;

    /**
     * The primary key of the table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = Constants::FLD_SUBMISSIONS_ID;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        Constants::FLD_SUBMISSIONS_SUBMISSION_ID,
        Constants::FLD_SUBMISSIONS_EXECUTION_TIME,
        Constants::FLD_SUBMISSIONS_CONSUMED_MEMORY,
        Constants::FLD_SUBMISSIONS_VERDICT
    ];

    public function user()
    {
        return $this->belongsTo(User::class, Constants::FLD_SUBMISSIONS_USER_ID);
    }

    public function problem()
    {
        return $this->belongsTo(Problem::class, Constants::FLD_SUBMISSIONS_PROBLEM_ID);
    }

    public function language()
    {
        return $this->belongsTo(Language::class, Constants::FLD_SUBMISSIONS_LANGUAGE_ID);
    }

    public function store()
    {
        $v = Validator::make($this->attributes, config('rules.submission.store_validation_rules'));
        $v->validate();
        $this->save();
    }
}
