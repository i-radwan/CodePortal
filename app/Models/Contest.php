<?php

namespace App\Models;

use App\Utilities\Constants;
use Illuminate\Database\Eloquent\Model;
use Validator;

class Contest extends Model
{
    public function __construct(array $attributes = [])
    {
        $this->fillable =  [
            Constants::FLD_CONTESTS_NAME,
            Constants::FLD_CONTESTS_TIME,
            Constants::FLD_CONTESTS_DURATION,
            Constants::FLD_CONTESTS_VISIBILITY
        ];
        parent::__construct($attributes);
    }

    public function participatingUsers()
    {
        return $this->belongsToMany(User::class, Constants::TBL_PARTICIPANTS)->withTimestamps();
    }

    public function organizingUsers()
    {
        return $this->belongsToMany(User::class, Constants::TBL_CONTEST_ADMIN);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function problems()
    {
        return $this->belongsToMany(Problem::class, Constants::TBL_CONTEST_PROBLEM);
    }

    public function store()
    {
        $v = Validator::make($this->attributes, config('rules.contest.store_validation_rules'));
        $v->validate();
        $this->save();
    }

}
