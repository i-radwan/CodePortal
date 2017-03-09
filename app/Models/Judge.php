<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Judge extends Model
{
    protected $fillable = ['name', 'link', 'api_link'];

    public function problems()
    {
        return $this->hasMany('App\Models\Problem');
    }

}
