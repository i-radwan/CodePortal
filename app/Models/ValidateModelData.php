<?php

namespace App\Models;

use Carbon\Carbon;
use Validator;

trait ValidateModelData
{
    /**
     * Validate the rules then save the model to the database
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
