<?php

namespace App\Service;

use App\Domain\DoctrineInsertedCoinRepository;

class ReturnMoneyService
{
    public function __construct(private DoctrineInsertedCoinRepository $insertedCoinRepository){}
    
    public function __invoke():array
    {
        $result = [];
        $insertedCoins = $this->insertedCoinRepository->getAll();

        foreach($insertedCoins as $insertedCoin){
            for($i = 0; $i < $insertedCoin->getQuantity(); $i++){
                $result[] = $insertedCoin->getCoinId()->getValue()->value/100;
            }
        }

        $this->insertedCoinRepository->deleteAll();

        return $result;
    }
}