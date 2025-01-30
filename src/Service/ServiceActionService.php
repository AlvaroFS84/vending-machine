<?php

namespace App\Service;

use App\Domain\Interface\RepositoryInterface;
use App\Exceptions\BadJsonContentException;

class ServiceActionService {

    public function __construct(
        private RepositoryInterface $coinRepository,
        private RepositoryInterface $productRepository
    ){}

    public function __invoke(array $data): void
    {
        if(isset($data['change'])){
            $this->coinRepository->deleteAll();
            foreach ($data['change'] as $coin) {
                $this->coinRepository->create($coin);
            }
        }
        
        if(isset($data['items'])){
            $this->productRepository->deleteAll();
            foreach ($data['items'] as $item) {
                $this->productRepository->create($item);
            }
        }
    }
}