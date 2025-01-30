<?php

namespace App\Domain;

use App\Domain\Interface\RepositoryInterface;
use App\Entity\Product;
use App\Enum\ProductName;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineProductRepository implements RepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager) {}
    
    public function getAll(): array
    {
        return $this->entityManager->getRepository(Product::class)->findAll();
    }

    public function create(array $data): void
    {
        if(isset($data['name']) && isset($data['price']) && isset($data['quantity'])){
            $product = new Product();

            $product->setName(ProductName::from(strtoupper($data['name'])));
            $product->setPrice($data['price']);
            $product->setQuantity($data['quantity']);

            $this->entityManager->persist($product);
            $this->entityManager->flush(); 
        }
    }

    public function deleteAll(): void
    {
        $query = $this->entityManager->createQuery('DELETE FROM App\Entity\Product p');
        $query->execute();
    }
}
