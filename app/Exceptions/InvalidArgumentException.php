<?php namespace App\Exceptions;

class InvalidArgumentException extends BaseException
{
    public function __construct ($message = "Invalid argument.") {
        parent::__construct($message, 422);
    }
}