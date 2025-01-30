<?php

namespace App\Domain;

use App\Domain\Interface\RepositoryCreateInterface;
use App\Domain\Interface\RepositoryInterface;
use App\Entity\Product;
use App\Enum\ProductName;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineProductRepository implements RepositoryInterface, RepositoryCreateInterface
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function getAll(): array
    {
        return $this->entityManager->getRepository(Product::class)->findAll();
    }

    public function create(array $data): void
    {
        if (!isset($data['name']) || !isset($data['price']) || !isset($data['quantity'])) {
            return;
        }

        $product = $this->findByName($data['name']);

        if ($product) {
            $product->setQuantity($data['quantity']);
            $product->setPrice($data['price']);
        } else {

            $product = new Product();
            $product->setName(ProductName::from(strtoupper($data['name'])));
            $product->setPrice($data['price']);
            $product->setQuantity($data['quantity']);
            
        }

        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    public function findByName(string $name): ?Product
    {
        return $this->entityManager->getRepository(Product::class)->findOneBy(['name' => strtoupper($name)]);
    }
}
