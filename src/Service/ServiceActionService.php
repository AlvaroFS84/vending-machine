<?php

namespace App\Service;

use App\Domain\Interface\RepositoryInterface;

class ServiceActionService {

    public function __construct(
        private RepositoryInterface $coinRepository,
        private RepositoryInterface $productRepository
    ){}

    public function __invoke(array $data): void
    {
        if(isset($data['change'])){
            foreach ($data['change'] as $coin) {
                $this->coinRepository->create($coin);
            }
        }
        
        if(isset($data['items'])){
            foreach ($data['items'] as $item) {
                $this->productRepository->create($item);
            }
        }
    }
}