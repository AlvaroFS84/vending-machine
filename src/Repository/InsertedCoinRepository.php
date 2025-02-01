<?php

namespace App\Repository;

use App\Entity\Coin;
use App\Entity\InsertedCoin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InsertedCoin>
 */
class InsertedCoinRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InsertedCoin::class);
    }

    public function findByCoin(Coin $coin): ?InsertedCoin
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.coinId = :coin')
            ->setParameter('coin', $coin)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function deleteAll(): void
    {
        $this->createQueryBuilder('i')
            ->delete()
            ->where('1 = 1') 
            ->getQuery()
            ->execute();
    }

    public static function getTotalInserted(EntityManagerInterface $entityManager): int
    {
        
        $insertedCoinRepository = $entityManager->getRepository(InsertedCoin::class);
        $inserted = $insertedCoinRepository->findAll();
        $total = 0;

        foreach ($inserted as $insertedCoin) {
            $total += $insertedCoin->getCoinId()->getValue()->value * $insertedCoin->getQuantity();
        }

        return $total;
    }

}
