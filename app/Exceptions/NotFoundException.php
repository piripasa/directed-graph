<?php namespace App\Exceptions;

class NotFoundException extends BaseException
{
    public function __construct ($message = "Not found.") {
        parent::__construct($message, 404);
    }
}