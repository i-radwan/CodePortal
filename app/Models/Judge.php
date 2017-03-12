<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;
class Judge extends Model
{
    protected $fillable = ['name', 'link', 'api_link'];

    public function problems()
    {
        return $this->hasMany(Problem::class);
    }

    public function store()
    {
        $v = Validator::make($this->attributes, config('rules.judge.store_validation_rules'));
        $v->validate();
        $this->save();
    }
}
