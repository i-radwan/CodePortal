<?php

namespace App;

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
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function participating_in_contests()
    {
        return $this->belongsToMany('App\Contest', 'participants')->withTimestamps();
    }

    public function organizing_contests()
    {
        return $this->belongsToMany('App\Contest', 'contest_admin');
    }

    public function questions()
    {
        return $this->hasMany('App\Question');
    }

    public function contest_questions($contest_id)
    {
        return $this->hasMany('App\Question')->where('contest_id', '=', $contest_id);
    }

    public function handles()
    {
        return $this->belongsToMany('App\Judge', 'user_handles')->withPivot('handle');
    }
    public function submissions()
    {
        return $this->hasMany('App\Submission');
    }


}
