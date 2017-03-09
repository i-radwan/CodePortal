<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Problem extends Model
{
    protected $fillable = ['name', 'difficulty', 'accepted_count'];

    public function contests()
    {
        return $this->belongsToMany('App\Contest', 'contest_problem');
    }

    public function judge()
    {
        return $this->hasOne('App\Judge');
    }

    public function tags()
    {
        return $this->belongsToMany('App\Tag', 'problem_tag');
    }
    public function submissions()
    {
        return $this->hasMany('App\Submission');
    }

}
