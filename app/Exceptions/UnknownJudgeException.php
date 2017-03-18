<?php

namespace App\Exceptions;

class UnknownJudgeException extends \Exception
{
    /**
     * UnkownJudgeException constructor.
     */
    public function __construct()
    {
        $this->message = "No judge with such ID";
    }
}