<?php

namespace App\Models;

use App\Utilities\Constants;
use App\Utilities\Utilities;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;

class Judge extends Model
{
    use ValidateModelData;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Constants::TBL_JUDGES;

    /**
     * The primary key of the table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = Constants::FLD_JUDGES_ID;

    /**
     * Indicates if the primary key is auto incremented.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        Constants::FLD_JUDGES_ID,
        Constants::FLD_JUDGES_NAME,
        Constants::FLD_JUDGES_LINK
    ];

    /**
     * The rules to check against before saving the model
     *
     * @var array
     */
    protected $rules = [
        Constants::FLD_JUDGES_ID => 'required|unique:' . Constants::TBL_JUDGES . '|integer|min:0',
        Constants::FLD_JUDGES_NAME => 'required|unique:' . Constants::TBL_JUDGES . '|max:100',
        Constants::FLD_JUDGES_LINK => 'required|unique:' . Constants::TBL_JUDGES . '|max:100|url'
    ];

    /**
     * Return all problems of the current online judge
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function problems()
    {
        return $this->hasMany(Problem::class, Constants::FLD_PROBLEMS_JUDGE_ID);
    }

    /**
     * Return all submissions on the current online judge
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function submissions()
    {
        return $this->hasManyThrough(
            Submission::class,
            Problem::class,
            Constants::FLD_PROBLEMS_JUDGE_ID,
            Constants::FLD_SUBMISSIONS_PROBLEM_ID,
            Constants::FLD_JUDGES_ID
        );
    }

    /**
     * Return all users who have handles on the current online judge
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            Constants::TBL_USER_HANDLES,
            Constants::FLD_USER_HANDLES_JUDGE_ID,
            Constants::FLD_USER_HANDLES_USER_ID
        )->withPivot(Constants::FLD_USER_HANDLES_HANDLE);
    }
}
