<?php

namespace App\Exceptions;



class NonExistingValueException extends VendingException 
{
    public function __construct()
    {
        parent::__construct('You inserted a no valid coin value', 400);
    }
}