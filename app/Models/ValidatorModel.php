<?php

namespace App\Models;

use Validator;
use Illuminate\Database\Eloquent\Model;

class ValidatorModel extends Model
{
    /**
     * The rules to check against before saving the model
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Validate the rule then save the model to the database
     *
     * @param array $options
     * @return bool
     */
    public function save(array $options = [])
    {
        Validator::make($this->attributes, $this->rules)->validate();
        return parent::save($options);
    }
}
