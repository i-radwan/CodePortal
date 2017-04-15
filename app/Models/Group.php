<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Utilities\Constants;
use Illuminate\Database\Eloquent\Builder;

class Group extends Model
{
    use ValidateModelData;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Constants::TBL_GROUPS;

    /**
     * The primary key of the table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = Constants::FLD_GROUPS_ID;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        Constants::FLD_GROUPS_NAME,
        Constants::FLD_GROUPS_OWNER_ID
    ];

    /**
     * The rules to check against before saving the model
     *
     * @var array
     */
    protected $rules = [
        Constants::FLD_GROUPS_NAME => 'required|max:100',
        Constants::FLD_GROUPS_OWNER_ID => 'required|exists:' . Constants::TBL_USERS . ',' . Constants::FLD_USERS_ID,
    ];

    /**
     * Return the owner user of this group
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, Constants::FLD_GROUPS_OWNER_ID);
    }


    /**
     * Return all members of this group
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function members()
    {
        return $this->belongsToMany(
            User::class,
            Constants::TBL_GROUP_MEMBERS,
            Constants::FLD_GROUP_MEMBERS_GROUP_ID,
            Constants::FLD_GROUP_MEMBERS_USER_ID
        )->withTimestamps();
    }


}
