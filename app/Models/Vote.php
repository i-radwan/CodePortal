<?php

namespace App\Models;

use App\Utilities\Constants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Vote extends Model
{
    // Add Validation
    use ValidateModelData;

    // Use Soft Delete
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Constants::TBL_VOTES;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        Constants::FLD_VOTES_TYPE
    ];

    /**
     * The rules to check against before saving the model
     *
     * @var array
     */
    protected $rules = [
        // ToDo: @Samir Add Rules
        // Constants::FLD_VOTES_USER_ID => 'required|exists:'. Constants::TBL_USERS. ','. Constants::FLD_USERS_ID,
        // Constants::FLD_VOTES_VOTED_TYPE => "required|in: " . "[Post::class, Comment::class])"  ,
        // Constants::FLD_VOTES_VOTED_ID => 'nullable|exists' . Constants::TBL_COMMENTS . ','. Constants::FLD_COMMENTS_ID,
    ];

    /**
     * Returns the user who sent this notification
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, Constants::FLD_VOTES_USER_ID);
    }


    /**
     * Returns the resource that this vote is for (post, comment, ...etc)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|null
     */
    public function resource()
    {
        switch ($this[Constants::FLD_VOTES_RESOURCE_TYPE]) {
            case Constants::RESOURCE_VOTE_POST:
                $class = Contest::class;
                break;
            case Constants::RESOURCE_VOTE_COMMENT:
                $class = Group::class;
                break;
            default:
                return null;
        }

        return $this->belongsTo($class, Constants::FLD_VOTES_RESOURCE_ID);
    }

    /**
     * Scope a query to only include votes related to a certain resource
     *
     * @param Builder $query
     * @param null $id
     * @return Builder
     */
    public function scopeOfResource(Builder $query, $id = null)
    {
        if ($id == null) {
            return $query;
        }

        $query->where(
            Constants::TBL_VOTES . '.' . Constants::FLD_VOTES_RESOURCE_ID,
            '=',
            $id
        );

        return $query;
    }

    /**
     * Scope a query to only include votes related to a certain resource type (post, comment ..etc)
     *
     * @param Builder $query
     * @param string|null $type
     * @return Builder
     */
    public function scopeOfResourceType(Builder $query, $type = null)
    {
        if ($type == null) {
            return $query;
        }

        $query->where(
            Constants::TBL_VOTES . '.' . Constants::FLD_VOTES_RESOURCE_TYPE,
            '=',
            $type
        );

        return $query;
    }

    /**
     * Scope a query to only include votes of certain type (up, down)
     *
     * @param Builder $query
     * @param $type
     * @return Builder
     */
    public function scopeOfType(Builder $query, $type)
    {
        if ($type == null) {
            return $query;
        }

        $query->where(
            Constants::TBL_VOTES . '.' . Constants::FLD_VOTES_TYPE,
            '=',
            $type
        );

        return $query;
    }

    /**
     * Scope a query to only include votes of certain type (up, down)
     *
     * @param Builder $query
     * @param $userId
     * @return Builder
     */
    public function scopeOfUser(Builder $query, $userId)
    {
        if ($userId == null) {
            return $query;
        }

        $query->where(
            Constants::TBL_VOTES . '.' . Constants::FLD_VOTES_USER_ID,
            '=',
            $userId
        );

        return $query;
    }
}
