<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = ['submission_id', 'execution_time', 'used_memory', 'verdict'];


    public function problem()
    {
        return $this->belongsTo('App\Models\Problem');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function language()
    {
        return $this->belongsTo('App\Models\Language');
    }

}
