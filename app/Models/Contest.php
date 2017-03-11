<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;

class Contest extends Model
{
    protected $fillable = ['name', 'time', 'duration', 'visibility'];

    public function participating_users()
    {
        return $this->belongsToMany('App\Models\User', 'participants')->withTimestamps();
    }

    public function organizing_users()
    {
        return $this->belongsToMany('App\Models\User', 'contest_admin');
    }

    public function questions()
    {
        return $this->hasMany('App\Models\Question');
    }

    public function problems()
    {
        return $this->belongsToMany('App\Models\Problem', 'contest_problem');
    }

    public function store()
    {
        $v = Validator::make($this->attributes, config('rules.contest.store_validation_rules'));
        $v->validate();
        $this->save();
    }

}
