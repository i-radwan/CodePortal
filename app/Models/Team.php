<?php

namespace App\Models;

use App\Utilities\Constants;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use ValidateModelData;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Constants::TBL_TEAMS;

    /**
     * The primary key of the table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = Constants::FLD_TEAMS_ID;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        Constants::FLD_TEAMS_NAME
    ];

    /**
     * The rules to check against before saving the model
     *
     * @var array
     */
    protected $rules = [
        Constants::FLD_TEAMS_NAME => 'required|max:50'
    ];

    /**
     * Return all the members of the team
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function members()
    {
        return $this->belongsToMany(
            User::class,
            Constants::TBL_TEAM_MEMBERS,
            Constants::FLD_TEAM_MEMBERS_TEAM_ID,
            Constants::FLD_TEAM_MEMBERS_USER_ID
        )->withTimestamps();
    }

    /**
     * Return the contests that the current team participated in
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function participatingContests()
    {
        return $this->belongsToMany(
            Contest::class,
            Constants::TBL_CONTEST_TEAMS,
            Constants::FLD_CONTEST_TEAMS_TEAM_ID,
            Constants::FLD_CONTEST_TEAMS_CONTEST_ID
        )->withTimestamps();
    }
}