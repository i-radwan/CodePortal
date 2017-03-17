<?php

namespace App\Exceptions;

class UnknownAdminException extends \Exception
{
    /**
     * UnkownAdminException constructor.
     */
    public function __construct()
    {
        $this->message = "No admin with such ID";
    }
}