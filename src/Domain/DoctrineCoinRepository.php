<?php

namespace App\Domain;

use App\Domain\Interface\RepositoryCreateInterface;
use App\Domain\Interface\RepositoryInterface;
use App\Entity\Coin;
use App\Enum\CurrencyValue;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineCoinRepository implements RepositoryInterface,RepositoryCreateInterface
{
    public function __construct(private EntityManagerInterface $entityManager) {}
    
    public function getAll(): array
    {
        return $this->entityManager->getRepository(Coin::class)->findAll();
    }

    public function create(array $data): void
    {
        if (isset($data['value']) && isset($data['quantity'])) {
            $coin = new Coin();       

            $coin->setValue(CurrencyValue::from($data['value']));
            $coin->setQuantity($data['quantity']);
        
            $this->entityManager->persist($coin);
            $this->entityManager->flush(); 
        }
    }

    public function deleteAll(): void
    {
        $query = $this->entityManager->createQuery('DELETE FROM App\Entity\Coin c');
        $query->execute();
    }

    public function findByValue(int $value): ?Coin
    {
        return $this->entityManager->getRepository(Coin::class)->findOneBy(['value' => $value]);
    }
}
