<?php

namespace App\Service;

use App\Entity\Coin;
use App\Entity\InsertedCoin;
use App\Exceptions\NonExistingValueException;
use Doctrine\ORM\EntityManagerInterface;

class InsertService{

    public function __construct(
        private EntityManagerInterface $entityManager
    ){}
   
    public function __invoke(array $coinData):void
    {
        if(isset($coinData['value'])){
            
            $coin = $this->entityManager->getRepository(Coin::class)->findOneBy([ 'value' => $coinData['value']*100]);

            if(!$coin){
                throw new  NonExistingValueException();
            }
                
            $insertedCoin = $this->entityManager->getRepository(InsertedCoin::class)->findByCoin($coin);
            
            if($insertedCoin){
                $insertedCoin->setQuantity($insertedCoin->getQuantity()+1);
            }else{
                $insertedCoin = new InsertedCoin();
                $insertedCoin->setCoin($coin);
                $insertedCoin->setQuantity(1);
            }

            $this->entityManager->persist($insertedCoin);
            $this->entityManager->flush(); 
        }
    }
}