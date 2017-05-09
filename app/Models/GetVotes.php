<?php

namespace App\Models;

use App\Utilities\Constants;

trait GetVotes
{
    /**
     * Return all post down votes
     * @return mixed
     */
    public function downVotes()
    {
        return $this->votes()->ofType(Constants::RESOURCE_VOTE_TYPE_DOWN);
    }

    /**
     * Return all post down votes
     * @return mixed
     */
    public function upVotes()
    {
        return $this->votes()->ofType(Constants::RESOURCE_VOTE_TYPE_UP);
    }

    /**
     * Check if the given user has up voted this resource
     *
     * @param User $user
     * @return bool
     */
    public function didUserUpVote(User $user)
    {
        return ($this->upVotes()->ofUser($user[Constants::FLD_USERS_ID])->count() > 0);
    }

    /**
     * Check if the given user has down voted this resource
     *
     * @param User $user
     * @return bool
     */
    public function didUserDownVote(User $user)
    {
        return ($this->downVotes()->ofUser($user[Constants::FLD_USERS_ID])->count() > 0);
    }
}

