<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = ['name'];

    public function submissions()
    {
        return $this->hasMany('App\Models\Submission');
    }
}
