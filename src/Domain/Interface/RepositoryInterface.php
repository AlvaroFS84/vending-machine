<?php

namespace App\Domain\Interface;

interface RepositoryInterface
{

    public function getAll(): array;
    public function create(array $data): void;
    public function deleteAll(): void;
}