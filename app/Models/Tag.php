<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name'];


    public function problems()
    {
        return $this->belongsToMany('App\Models\Problem', 'problem_tag');
    }

}
