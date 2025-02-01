<?php

namespace App\Service;

use App\Entity\Coin;
use App\Entity\Product;
use App\Enum\CurrencyValue;
use App\Enum\ProductName;
use Doctrine\ORM\EntityManagerInterface;

class ServiceActionService 
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function __invoke(array $data): void
    {
        if (!empty($data['change'])) {
            $this->insertCoins($data['change']);
        }
        
        if (!empty($data['items'])) {
            $this->insertProducts($data['items']);
        }

        $this->entityManager->flush();
    }

    private function insertCoins(array $coins): void
    {
        foreach ($coins as $coinData) {
            $coin = $this->entityManager->getRepository(Coin::class)
                ->findOneBy(['value' => $coinData['value']]) ?? new Coin();
            
            $coin->setValue(CurrencyValue::from($coinData['value']));
            $coin->setQuantity($coinData['quantity']);

            $this->entityManager->persist($coin);
        }
    }

    private function insertProducts(array $items): void
    {
        foreach ($items as $item) {
            $product = $this->entityManager->getRepository(Product::class)
                ->findOneBy(['name' => strtoupper($item['name'])]) ?? new Product();
            
            $product->setName(ProductName::from(strtoupper($item['name'])));
            $product->setPrice($item['price']);
            $product->setQuantity($item['quantity']);

            $this->entityManager->persist($product);
        }
    }
}
