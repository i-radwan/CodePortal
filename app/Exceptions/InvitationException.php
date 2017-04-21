<?php

namespace App\Exceptions;

class InvitationException extends \Exception
{
    /**
     * InvitationException constructor.
     *
     * @param string $message
     */
    public function __construct($message)
    {
        $this->message = $message;
    }
}