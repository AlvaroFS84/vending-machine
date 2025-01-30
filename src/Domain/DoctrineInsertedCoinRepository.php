<?php

namespace App\Domain;

use App\Domain\Interface\RepositoryInterface;
use App\Entity\Coin;
use App\Entity\InsertedCoin;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineInsertedCoinRepository implements RepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager) {}
    
    public function getAll(): array
    {
        return $this->entityManager->getRepository(InsertedCoin::class)->findAll();
    }

   
    public function deleteAll(): void
    {
        $query = $this->entityManager->createQuery('DELETE FROM App\Entity\InsertedCoin i');
        $query->execute();
    }

    public function findByCoin(Coin $coin): ?InsertedCoin
    {
        return $this->entityManager->getRepository(InsertedCoin::class)->findOneBy(['coinId' => $coin]);
    }
}
