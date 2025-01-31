<?php

namespace App\Exceptions;

class MalformedProductOrderException extends VendingException{
    
    public function __construct()
    {
        parent::__construct('Malformed product order,shoud starts with GET-', 400);
    }
}