<?php

namespace App\Models;

use App\Utilities\Constants;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use ValidateModelData;

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

    /**
     * The rules to check against before saving the model
     *
     * @var array
     */
    protected $rules = [
        Constants::FLD_LANGUAGES_NAME => 'required|unique:' . Constants::TBL_LANGUAGES . '|max:50'
    ];

    /**
     * Return all submissions written by the current programming language
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function submissions()
    {
        return $this->hasMany(Submission::class, Constants::FLD_SUBMISSIONS_LANGUAGE_ID);
    }
}