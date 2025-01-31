<?php

namespace App\Exceptions;


class MissingSelectionException extends VendingException
{
    public function __construct()
    {
        parent::__construct('Please select a product',400);
    }
}