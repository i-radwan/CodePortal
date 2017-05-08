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
}

