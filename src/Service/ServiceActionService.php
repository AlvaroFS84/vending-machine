<?php

namespace App\Service;

use App\Entity\Coin;
use App\Entity\Product;
use App\Enum\CurrencyValue;
use App\Enum\ProductName;
use Doctrine\ORM\EntityManagerInterface;

class ServiceActionService {

    public function __construct(
        private EntityManagerInterface $entityManager,
    ){}

    public function __invoke(array $data): void
    {
        // Insert coins
        if(isset($data['change'])){
            foreach ($data['change'] as $coinToInsert) {
                $coin = $this->entityManager->getRepository(Coin::class)->findOneBy(['value' => $coinToInsert['value']])??new Coin();
                $coin->setValue(CurrencyValue::from($coinToInsert['value']));
                $coin->setQuantity($coinToInsert['quantity']);

                $this->entityManager->persist($coin);
            }

            $this->entityManager->flush();
        }
        
        // Insert products
        if(isset($data['items'])){
            foreach ($data['items'] as $item) {
                $product = $this->entityManager->getRepository(Product::class)->findOneBy(['name' => $item['name']])??new Product();
                $product->setName(ProductName::from(strtoupper($item['name'])));
                $product->setPrice($item['price']);
                $product->setQuantity($item['quantity']);

                $this->entityManager->persist($product);
            }

            $this->entityManager->flush();
        }
    }
}