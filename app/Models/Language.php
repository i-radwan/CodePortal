<?php

namespace App\Models;

use Validator;
use App\Utilities\Constants;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Constants::TBL_LANGUAGES;

    /**
     * The primary key of the table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = Constants::FLD_LANGUAGES_ID;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        Constants::FLD_LANGUAGES_NAME
    ];

    public function submissions()
    {
        return $this->hasMany(Submission::class, Constants::FLD_SUBMISSIONS_LANGUAGE_ID);
    }

    public function store()
    {
        $v = Validator::make($this->attributes, config('rules.language.store_validation_rules'));
        $v->validate();
        $this->save();
    }
}