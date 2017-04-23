<?php

namespace App\Models;
use App\Utilities\Constants;

trait GetUserVotes
{
    /*
    * Get the users who voted up the object and haven't un liked it (SoftDelete)
     * */
    public function upVotes(){
        return $this->morphToMany(User::class, Constants::TBL_UP_VOTES)->whereDeletedAt(null);
    }

    /*
    * Check if the current user voted Up the object or not
    */
    public function isUpVoted(){
        $upVote = $this['upVotes']->where(Constants::FLD_USERS_ID,\Auth::id())->first();
        return (!is_null($upVote)) ? true : false;
    }

    /*
    * Get the users who voted up the object and haven't un liked it (SoftDelete)
     * */
    public function downVotes(){
        return $this->morphToMany(User::class, Constants::TBL_DOWN_VOTES)->whereDeletedAt(null);
    }

    /*
    * Check if the current user voted Up the object or not
    */
    public function isDownVoted(){
        $downVote = $this['downVotes']->where(Constants::FLD_USERS_ID,\Auth::id())->first();
        return (!is_null($downVote)) ? true : false;
    }
}

