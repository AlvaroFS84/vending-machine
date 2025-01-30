<?php

namespace App\Exceptions;



class BadJsonContentException extends VendingException 
{
    public function __construct()
    {
        parent::__construct('Bad JSON content', 400);
    }
}