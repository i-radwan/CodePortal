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
        'name', 'email', 'password', 'handle', 'gender', 'age', 'profile_pic', 'country'
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
        return $this->belongsToMany('App\Models\Contest', 'participants')->withTimestamps();
    }

    public function organizing_contests()
    {
        return $this->belongsToMany('App\Models\Contest', 'contest_admin');
    }

    public function questions()
    {
        return $this->hasMany('App\Models\Question');
    }

    public function answered_questions()
    {
        return $this->hasMany('App\Models\Question')->where('admin_id', '=', $this->id);
    }

    public function contest_questions($contest_id)
    {
        return $this->hasMany('App\Models\Question')->where('contest_id', '=', $contest_id);
    }

    public function handles()
    {
        return $this->belongsToMany('App\Models\Judge', 'user_handles')->withPivot('handle');
    }
    public function submissions()
    {
        return $this->hasMany('App\Models\Submission');
    }

}
