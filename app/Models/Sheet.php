<?php

namespace App\Models;

use App\Utilities\Constants;
use Illuminate\Database\Eloquent\Model;

class Sheet extends Model
{
    use ValidateModelData;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Constants::TBL_SHEETS;

    /**
     * The primary key of the table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = Constants::FLD_SHEETS_ID;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        Constants::FLD_SHEETS_NAME,
        Constants::FLD_SHEETS_GROUP_ID
    ];

    /**
     * The rules to check against before saving the model
     *
     * @var array
     */
    protected $rules = [
        Constants::FLD_SHEETS_NAME => 'required|max:50',
        Constants::FLD_SHEETS_GROUP_ID => 'required|exists:' . Constants::TBL_GROUPS . ',' . Constants::FLD_GROUPS_ID
    ];

    /**
     * Return all problems of this sheet
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function problems()
    {
        return $this->belongsToMany(
            Problem::class,
            Constants::TBL_SHEETS_PROBLEMS,
            Constants::FLD_SHEETS_PROBLEMS_SHEET_ID,
            Constants::FLD_SHEETS_PROBLEMS_PROBLEM_ID
        )->withPivot(Constants::FLD_SHEETS_PROBLEMS_SOLUTION)->withTimestamps();
    }

    /**
     * Return the group which this sheet belongs to
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(Group::class, Constants::FLD_SHEETS_GROUP_ID);
    }


    /**
     * Delete the sheet after removing all of its relations records
     *
     * @return bool|null
     */
    public function delete()
    {
        $this->problems()->detach();
        return parent::delete();
    }

}