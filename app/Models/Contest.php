<?php

namespace App\Models;

use Validator;
use App\Utilities\Constants;
use Illuminate\Database\Eloquent\Model;

class Contest extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Constants::TBL_CONTESTS;

    /**
     * The primary key of the table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = Constants::FLD_CONTESTS_ID;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        Constants::FLD_CONTESTS_NAME,
        Constants::FLD_CONTESTS_TIME,
        Constants::FLD_CONTESTS_DURATION,
        Constants::FLD_CONTESTS_VISIBILITY
    ];

    public function problems()
    {
        return $this->belongsToMany(
            Problem::class,
            Constants::TBL_CONTEST_PROBLEMS,
            Constants::FLD_CONTEST_PROBLEMS_CONTEST_ID,
            Constants::FLD_CONTEST_PROBLEMS_PROBLEM_ID
        );
    }

    public function participatingUsers()
    {
        return $this->belongsToMany(
            User::class,
            Constants::TBL_CONTEST_PARTICIPANTS,
            Constants::FLD_CONTEST_PARTICIPANTS_CONTEST_ID,
            Constants::FLD_CONTEST_PARTICIPANTS_USER_ID
        )->withTimestamps();
    }

    public function organizingUsers()
    {
        return $this->belongsToMany(
            User::class,
            Constants::TBL_CONTEST_ADMINS,
            Constants::FLD_CONTEST_ADMINS_CONTEST_ID,
            Constants::FLD_CONTEST_ADMINS_ADMIN_ID
        );
    }

    public function questions()
    {
        return $this->hasMany(Question::class, Constants::FLD_QUESTIONS_CONTEST_ID);
    }

    public function store()
    {
        $v = Validator::make($this->attributes, config('rules.contest.store_validation_rules'));
        $v->validate();
        $this->save();
    }
}
