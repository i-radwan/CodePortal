<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;
class Judge extends Model
{
    protected $fillable = ['name', 'link', 'api_link'];

    public function problems()
    {
        return $this->hasMany('App\Models\Problem');
    }

    public function store()
    {
        $v = Validator::make(['name' => $this->name, 'link' => $this->link, 'api_link' => $this->api_link], config('rules.judge.store_validation_rules'));
        $v->validate();
        $this->save();
    }
}
