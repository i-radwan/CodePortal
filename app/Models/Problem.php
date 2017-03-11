<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\UnknownJudgeException;
use Validator;

class Problem extends Model
{
    protected $fillable = ['name', 'difficulty', 'accepted_count'];

    public function contests()
    {
        return $this->belongsToMany('App\Models\Contest', 'contest_problem');
    }

    public function judge()
    {
        return $this->belongsTo('App\Models\Judge');
    }

    public function tags()
    {
        return $this->belongsToMany('App\Models\Tag', 'problem_tag');
    }

    public function submissions()
    {
        return $this->hasMany('App\Models\Submission');
    }

    public function save(array $options = [])
    {
        $v = Validator::make($this->attributes, config('rules.problem.store_validation_rules'));
        $v->validate();

        if (!$this->judge()) {
            throw new UnknownJudgeException;
        }
        return parent::save($options);
    }
}
