<?php

namespace App\Domain\Interface;

interface RepositoryCreateInterface
{
    public function create(array $data): void;   
}