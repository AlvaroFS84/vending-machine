<?php

namespace App\Service;

use App\Domain\InsertedCoinRepository;
use App\Domain\Interface\RepositoryInterface;
use App\Entity\InsertedCoin;
use App\Exceptions\NonExistingValueException;
use Doctrine\ORM\EntityManagerInterface;

class InsertService{

    public function __construct(
        private RepositoryInterface $coinRepository,
        private RepositoryInterface $doctrineInsertedCoinRepository,
        private EntityManagerInterface $entityManager
    ){}
   
    public function __invoke(array $coin):void
    {
        if(isset($coin['value'])){
            
            $coin = $this->coinRepository->findByValue($coin['value']*100);

            if(!$coin){
                throw new  NonExistingValueException();
            }
                
            $insertedCoin = $this->doctrineInsertedCoinRepository->findByCoin($coin);
            
            if($insertedCoin){
                $insertedCoin->setQuantity($insertedCoin->getQuantity()+1);
            }else{
                $insertedCoin = new InsertedCoin();
                $insertedCoin->setCoinId($coin);
                $insertedCoin->setQuantity(1);
            }

            $this->entityManager->persist($insertedCoin);
            $this->entityManager->flush(); 
        }
    }
}