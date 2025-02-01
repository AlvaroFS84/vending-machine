<?php

namespace App\Service;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class ProductService{

    public function __construct(
        private EntityManagerInterface $entityManager
    ){}

    public function decreaseProductNumber(Product $product):void
    {
        $product->setQuantity($product->getQuantity() - 1);
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }
}