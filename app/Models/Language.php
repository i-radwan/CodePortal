<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;

class Language extends Model
{
    public function __construct(array $attributes = [])
    {
        $this->fillable =  [
            config('db_constants.FIELDS.FLD_LANGUAGES_NAME'),
        ];
        parent::__construct($attributes);
    }
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function store()
    {
        $v = Validator::make($this->attributes, config('rules.language.store_validation_rules'));
        $v->validate();
        $this->save();
    }
}
