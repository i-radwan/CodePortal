<?php

namespace App\Exceptions;

class UnknownContestException extends \Exception
{
    /**
     * UnkownContestException constructor.
     */
    public function __construct()
    {
        $this->message = "No contest with such ID";
    }
}