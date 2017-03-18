<?php

namespace App\Exceptions;

class UnknownUserException extends \Exception
{
    /**
     * UnkownUserException constructor.
     */
    public function __construct()
    {
        $this->message = "No user with such ID";
    }
}