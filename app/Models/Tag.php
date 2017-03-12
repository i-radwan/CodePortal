<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;

class Tag extends Model
{
    protected $fillable = ['name'];


    public function problems()
    {
        return $this->belongsToMany(Problem::class, config('db_constants.TABLES.TBL_PROBLEM_TAG'));
    }

    public function store()
    {
        $v = Validator::make($this->attributes, config('rules.tag.store_validation_rules'));
        $v->validate();
        $this->save();
    }
}
