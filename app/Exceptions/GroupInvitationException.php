<?php

namespace App\Exceptions;

class GroupInvitationException extends \Exception
{
    /**
     * GroupInvitationException constructor.
     *
     * @param string $message
     */
    public function __construct($message)
    {
        $this->message = $message;
    }
}