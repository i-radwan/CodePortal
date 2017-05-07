<?php

namespace App\Models;

use App\Utilities\Constants;

trait GetUserVotes
{
    /**
     * Get the users who voted up the object and haven't un liked it (SoftDelete)
     *
     * @return mixed
     */
    public function upVotes()
    {
        return $this->morphToMany(User::class, Constants::TBL_VOTES)
            ->whereType(Constants::RESOURCE_VOTE_TYPE_UP)
            ->whereDeletedAt(null);
    }

    /**
     * Check if the current user voted Up the object or not
     *
     * @return bool
     */
    public function isUpVoted()
    {
        $upVote = $this->upVotes()
            ->where(Constants::FLD_USERS_ID, \Auth::user()[Constants::FLD_USERS_ID])->first();

        return ($upVote == null);
    }

    /**
     * Get the users who voted up the object and haven't un liked it (SoftDelete)
     *
     * @return mixed
     */
    public function downVotes()
    {
        return $this->morphToMany(User::class, Constants::TBL_VOTES)
            ->whereType(Constants::RESOURCE_VOTE_TYPE_DOWN)
            ->whereDeletedAt(null);
    }

    /**
     * Check if the current user voted Up the object or not
     *
     * @return bool
     */
    public function isDownVoted()
    {
        $downVote = $this->downVotes()
            ->where(Constants::FLD_USERS_ID, \Auth::user()[Constants::FLD_USERS_ID])->first();
        return ($downVote == null);
    }
}

