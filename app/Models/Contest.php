<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;

class Contest extends Model
{
    protected $fillable = ['name', 'time', 'duration', 'visibility'];

    public function participatingUsers()
    {
        return $this->belongsToMany(User::class, config('db_constants.TABLES.TBL_PARTICIPANTS'))->withTimestamps();
    }

    public function organizingUsers()
    {
        return $this->belongsToMany(User::class, config('db_constants.TABLES.TBL_CONTEST_ADMIN'));
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function problems()
    {
        return $this->belongsToMany(Problem::class, config('db_constants.TABLES.TBL_CONTEST_PROBLEM'));
    }

    public function store()
    {
        $v = Validator::make($this->attributes, config('rules.contest.store_validation_rules'));
        $v->validate();
        $this->save();
    }

}
