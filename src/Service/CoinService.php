<?php

namespace App\Service;

use App\Entity\Coin;
use Doctrine\ORM\EntityManagerInterface;

class CoinService{

    public function __construct(
        private EntityManagerInterface $entityManager
    ){}

    public function deleteSelectCoinstoChangeFromAvailableCoins(array $change):void
    {
        foreach($change as $coin){
            $coinEntity = $this->entityManager->getRepository(Coin::class)->findOneBy(['value' => $coin]);
            $coinEntity->setQuantity($coinEntity->getQuantity() - 1);
            $this->entityManager->persist($coinEntity);
        }
        $this->entityManager->flush();
    }

    public function addInsertedCoinsToAvaliableCoins(array $insertedCoins):void
    {
        foreach($insertedCoins as $insertedCoin){
            $coin = $this->entityManager->getRepository(Coin::class)->findOneBy(['value' => $insertedCoin->getCoin()->getValue()->value]);
            $coin->setQuantity($coin->getQuantity() + $insertedCoin->getQuantity());
            $this->entityManager->persist($coin);
            $this->entityManager->remove($insertedCoin);
        }
        $this->entityManager->flush();
    }
}