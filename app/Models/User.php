<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // ToDo remove role from fillables
    protected $fillable = [
        'name', 'email', 'password', 'username', 'gender', 'age', 'profile_pic', 'country'
    ];
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
        return $this->belongsToMany(Contest::class, config('db_constants.TABLES.TBL_PARTICIPANTS'))->withTimestamps();
    }

    public function organizingContests()
    {
        return $this->belongsToMany(Contest::class, config('db_constants.TABLES.TBL_CONTEST_ADMIN'));
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function answered_questions()
    {
        return $this->hasMany(Question::class)->where(config('db_constants.FIELDS.FLD_QUESTIONS_ADMIN_ID'), '=', $this->id);
    }

    public function contest_questions($contest_id)
    {
        return $this->hasMany(Question::class)->where(config('db_constants.FIELDS.FLD_QUESTIONS_CONTEST_ID'), '=', $contest_id);
    }

    public function handles()
    {
        return $this->belongsToMany(Judge::class, config('db_constants.TABLES.TBL_USER_HANDLES'))->withPivot(config('db_constants.FIELDS.FLD_USER_HANDLES_HANDLE'));
    }
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

}
