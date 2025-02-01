<?php

namespace App\Exceptions;



class NonExistingProductException extends VendingException 
{
    public function __construct()
    {
        parent::__construct('The requested product doesn\'t exists', 400);
    }
}