<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;
class Judge extends Model
{
    public function __construct(array $attributes = [])
    {
        $this->fillable =  [
            config('db_constants.FIELDS.FLD_JUDGES_NAME'),
            config('db_constants.FIELDS.FLD_JUDGES_LINK'),
            config('db_constants.FIELDS.FLD_JUDGES_API_LINK'),
        ];
        parent::__construct($attributes);
    }

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
