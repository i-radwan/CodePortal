<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = ['submission_id', 'execution_time', 'used_memory', 'verdict'];


    public function problem()
    {
        return $this->belongsTo('App\Problem');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function language()
    {
        return $this->belongsTo('App\Language');
    }

}
