<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Utilities\Constants;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public function __construct(array $attributes = [])
    {
        $this->fillable =  [
            Constants::FLD_USERS_NAME,
            Constants::FLD_USERS_EMAIL,
            Constants::FLD_USERS_PASSWORD,
            Constants::FLD_USERS_USERNAME,
            Constants::FLD_USERS_GENDER,
            Constants::FLD_USERS_AGE,
            Constants::FLD_USERS_PROFILE_PIC,
            Constants::FLD_USERS_COUNTRY
        ];
        parent::__construct($attributes);
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function participatingContests()
    {
        return $this->belongsToMany(Contest::class, Constants::TBL_PARTICIPANTS)->withTimestamps();
    }

    public function organizingContests()
    {
        return $this->belongsToMany(Contest::class, Constants::TBL_CONTEST_ADMIN);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function answered_questions()
    {
        return $this->hasMany(Question::class)->where(Constants::FLD_QUESTIONS_ADMIN_ID, '=', $this->id);
    }

    public function contest_questions($contest_id)
    {
        return $this->hasMany(Question::class)->where(Constants::FLD_QUESTIONS_CONTEST_ID, '=', $contest_id);
    }

    public function handles()
    {
        return $this->belongsToMany(Judge::class, Constants::TBL_USER_HANDLES)->withPivot(Constants::FLD_USER_HANDLES_HANDLE);
    }
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

}
