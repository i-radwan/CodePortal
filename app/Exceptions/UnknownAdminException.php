<?php
/**
 * Created by PhpStorm.
 * User: ibrahimradwan
 * Date: 3/10/17
 * Time: 6:17 PM
 */

namespace App\Exceptions;


class UnknownAdminException extends \Exception
{

    /**
     * UnkownJudgeException constructor.
     */
    public function __construct()
    {
        $this->message = "No admin with such ID";
    }
}