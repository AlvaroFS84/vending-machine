<?php

namespace App\Service;

use App\Entity\InsertedCoin;
use Doctrine\ORM\EntityManagerInterface;

class ReturnMoneyService
{
    public function __construct(private EntityManagerInterface $entityManager){}
    
    public function __invoke():array
    {
        $result = [];
        $insertedCoins = $this->entityManager->getRepository(InsertedCoin::class)->findAll();

        foreach($insertedCoins as $insertedCoin){
            for($i = 0; $i < $insertedCoin->getQuantity(); $i++){
                $result[] = number_format(($insertedCoin->getCoinId()->getValue()->value/100), 2, '.', '');
            }
        }

        $this->entityManager->getRepository(InsertedCoin::class)->deleteAll();

        return $result;
    }
}