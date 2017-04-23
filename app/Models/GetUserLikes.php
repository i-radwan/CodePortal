<?php

namespace App\Models;

trait GetUserLikes
{
    /*
    * Get the users who liked the object and haven't un liked it (SoftDelete)
     * */
    public function likes(){
        return $this->morphToMany(User::class, 'likeable')->whereDeletedAt(null);
    }

    /*
    * Check if the current user liked the object or not
    */
    public function getIsLiked(){
        $like = $this['likes']->whereUserId(\Auth::id()->first);
        return (!is_null($like)) ? true : false;
    }

}

