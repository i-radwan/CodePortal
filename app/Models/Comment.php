<?php

namespace App\Models;


use App\Utilities\Constants;


use Illuminate\Database\Eloquent\Model;
use PhpParser\Builder;


class Comment extends Model
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
    protected $table = Constants::TBL_COMMENTS;

    /**
     * The primary key of the table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = Constants::FLD_COMMENTS_ID;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        Constants::FLD_COMMENTS_BODY
    ];

    /**
     * The rules to check against before saving the model
     *
     * @var array
     */
    protected $rules = [
        Constants::FLD_COMMENTS_BODY => 'required|min:3',
        Constants::FLD_COMMENTS_USER_ID => 'required|exists:' . Constants::TBL_USERS . ',' . Constants::FLD_USERS_ID,
        Constants::FLD_COMMENTS_POST_ID => 'required|exists:' . Constants::TBL_POSTS . ',' . Constants::FLD_POSTS_ID,
        Constants::FLD_COMMENTS_PARENT_ID => 'nullable|exists:' . Constants::TBL_COMMENTS . ',' . Constants::FLD_COMMENTS_ID,
    ];

    /**
     * Delete the model from the database and its related data
     *
     * @return bool|null
     */
    public function delete()
    {
        // Delete votes
        foreach ($this->votes()->get() as $vote){
            $vote->forceDelete();
        }

        return parent::delete();
    }

    /**
     * Get Comment Owner User Name
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, Constants::FLD_COMMENTS_USER_ID);
    }

    /**
     * Get Comment Post
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(Post::class, Constants::FLD_COMMENTS_POST_ID);
    }

    /**
     * Get comment parent (null if not applicable)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Comment::class, Constants::FLD_COMMENTS_PARENT_ID);
    }

    /**
     * Get all replies to that Comment
     *
     * @return Comments Collection
     */
    public function replies()
    {
        return $this->hasMany(Comment::class, Constants::FLD_COMMENTS_PARENT_ID);
    }

    /**
     * Return all votes for this comment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function votes()
    {
        return $this
            ->hasMany(Vote::class, Constants::FLD_VOTES_RESOURCE_ID)
            ->ofResourceType(Constants::RESOURCE_VOTE_COMMENT);
    }
}
