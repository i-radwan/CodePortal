<?php

namespace App\Models;

use Validator;
use Illuminate\Database\Eloquent\Model;
use App\Utilities\Constants;

class Submission extends Model
{
    public function __construct(array $attributes = [])
    {
        $this->fillable =  [
            Constants::FLD_SUBMISSIONS_SUBMISSION_ID,
            Constants::FLD_SUBMISSIONS_EXECUTION_TIME,
            Constants::FLD_SUBMISSIONS_CONSUMED_MEMORY,
            Constants::FLD_SUBMISSIONS_VERDICT,
        ];
        parent::__construct($attributes);
    }

    public function problem()
    {
        return $this->belongsTo(Problem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function store()
    {
        $v = Validator::make($this->attributes, config('rules.submission.store_validation_rules'));
        $v->validate();

        $this->save();
    }
}
