<?php

namespace App\Models;

use Validator;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = ['submission_id', 'execution_time', 'consumed_memory', 'verdict'];


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
