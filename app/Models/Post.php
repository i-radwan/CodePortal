<?php

namespace App\Models;


use App\Utilities\Constants;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


class Post extends Model
{
    // Add Validation
    use ValidateModelData;

    // Trait to get resource up/down votes
    use GetVotes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Constants::TBL_POSTS;

    /**
     * The primary key of the table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = Constants::FLD_POSTS_ID;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        Constants::FLD_POSTS_TITLE,
        Constants::FLD_POSTS_BODY
    ];

    /**
     * The rules to check against before saving the model
     *
     * @var array
     */
    protected $rules = [
        Constants::FLD_POSTS_BODY => 'required|min:50',
        Constants::FLD_POSTS_TITLE => 'required|min:6|max:100',
        Constants::FLD_POSTS_OWNER_ID => 'required|exists:' . Constants::TBL_USERS . ',' . Constants::FLD_USERS_ID
    ];

    /**
     * Get comments for this Post
     */
    public function comments()
    {
        return $this->hasMany(Comment::class)->where(Constants::FLD_COMMENTS_PARENT_ID, null);
    }

    /**
     * Get owner of the post
     */
    public function owner()
    {
        return $this->belongsTo(User::class, Constants::FLD_POSTS_OWNER_ID);
    }

    /**
     * Retrieve posts that have title or content such as givens
     *
     * @param Builder $query
     * @param null $word
     * @return Builder
     */
    public function scopeOfContent(Builder $query, $word = null)
    {
        //Check if name is empty or null
        if ($word == null || $word == "") {
            return $query;
        }

        return $query->where(Constants::TBL_POSTS . '.' . Constants::FLD_POSTS_BODY,
            'LIKE',
            "%$word%"
        )->orwhere(
            Constants::TBL_POSTS . '.' . Constants::FLD_POSTS_TITLE,
            'LIKE',
            "%$word%"
        );
    }

    /**
     * Return all votes for this post
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function votes()
    {
        return $this
            ->hasMany(Vote::class, Constants::FLD_VOTES_RESOURCE_ID)
            ->ofResourceType(Constants::RESOURCE_VOTE_POST);
    }
}
