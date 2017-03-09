<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name'];


    public function problems()
    {
        return $this->belongsToMany('App\Problem', 'problem_tag');
    }

}
