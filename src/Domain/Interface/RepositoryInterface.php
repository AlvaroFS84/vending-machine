<?php

namespace App\Domain\Interface;

interface RepositoryInterface
{
    public function getAll(): array;
}