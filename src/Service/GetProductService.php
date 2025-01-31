<?php

namespace App\Service;

use App\Entity\Coin;
use App\Entity\InsertedCoin;
use App\Entity\Product;
use App\Exceptions\NonExistingProductException;
use Doctrine\ORM\EntityManagerInterface;

class GetProductService{

    public function __construct(private EntityManagerInterface $entityManager){}

    public function __invoke(string $selection):array
    {
        $productName = strtoupper(explode('-', $selection)[1]);

        $product = $this->entityManager->getRepository(Product::class)->findOneBy(['name' => $productName]);

        if(!$product){
            throw new NonExistingProductException();
        }

        if($product->getQuantity() == 0){
            return [
                'message' => 'Product is out of stock',
            ];
        }else{
            $result = [];
            $amountInserted = $this->getTotalInserted();

            if($amountInserted < $product->getPrice()){
                $result[] = ['message' => 'Please insert more coins'];
            }elseif($amountInserted === $product->getPrice()){
                $result[] = ['message' => $product->getName()];
            }else{
                // Store the inserted coins in the machine
                $change = $this->calculateChange($product);
                if(empty($change)){
                    $result[] = ['message' => 'Cannot provide your change'];
                }else{
                    $this->deleteSelectCoinstoChangeFromAvailableCoins($change);
                    $this->addInsertedCoinsToAvaliableCoins();
                    $this->decreaseProductNumber($product);
                    $result[] = ['message' => [$product->getName(), ...$change]];
                }
                    
            }
                

            return $result;
        }
    }

    private function getTotalInserted(): int
    {
        $inserted = $this->entityManager->getRepository(InsertedCoin::class)->findAll();
        $total = 0;

        $total = array_reduce($inserted, function($total, $insertedCoin) {
            return $total + ($insertedCoin->getCoinId()->getValue()->value * $insertedCoin->getQuantity());
        }, $total);

        return $total;
    }

    private function calculateChange(Product $product): array
    {
        // Get the amount of change to return
        $totalInserted = $this->getTotalInserted();
        $changeToReturn = $totalInserted - $product->getPrice();

        // If there is no change to return
        if ($changeToReturn <= 0) {
            return [];
        }

        // Get all coins
        $coins = $this->entityManager->getRepository(Coin::class)->findBy([], ['value' => 'DESC']);
        $formattedCoins = $this->formatCoinsToGetChange($coins);
        // Container for the coins that will be returned
        $change = [];

        // Call backtracking to calculate the coins to return
        if ($this->findChange($formattedCoins, $changeToReturn, [], $change)) {
            return $change;
        } else {
            return [];
        }
    }

    /**
     * Format the coins for the response
     */
    private function formatCoinsToGetChange(array $coins):array
    {
        $formattedCoins = [];

        foreach ($coins as $coin) {
            for ($i = 0; $i < $coin->getQuantity(); $i++) {
                $formattedCoins[] = $coin->getValue()->value;
            }
        }

        return $formattedCoins;
    }

    private function findChange(array $coins, int $changeAmount, array $currentCombination, array &$result): bool
    {
        // If the change is exactly 0, we have found a valid combination
        if ($changeAmount == 0) {
            $result = $currentCombination;
            return true;
        }

        // If the change is negative, we go back
        if ($changeAmount < 0) {
            return false;
        }

        // Iterate through all available coins
        foreach ($coins as $key => $coin) {
            // Try adding the coin to the current combination
            $newCombination = array_merge($currentCombination, [$coin]);

            // Create a copy of the coin array without the current coin (to avoid modifying the original)
            $remainingCoins = $coins;
            unset($remainingCoins[$key]); // Remove only from the copy

            // Recursively call with the updated change amount and the copied array
            if ($this->findChange(array_values($remainingCoins), $changeAmount - $coin, $newCombination, $result)) {
                return true; // If a valid combination is found, return
            }
        }

        // If no valid solution is found
        return false;
    }

    public function deleteSelectCoinstoChangeFromAvailableCoins(array $change):void
    {
        foreach($change as $coin){
            $coinEntity = $this->entityManager->getRepository(Coin::class)->findOneBy(['value' => $coin]);
            $coinEntity->setQuantity($coinEntity->getQuantity() - 1);
            $this->entityManager->persist($coinEntity);
        }
        $this->entityManager->flush();
    }

    private function addInsertedCoinsToAvaliableCoins():void
    {
        $insertedCoins = $this->entityManager->getRepository(InsertedCoin::class)->findAll();
        foreach($insertedCoins as $insertedCoin){
            $coin = $this->entityManager->getRepository(Coin::class)->findOneBy(['value' => $insertedCoin->getCoinId()->getValue()->value]);
            $coin->setQuantity($coin->getQuantity() + $insertedCoin->getQuantity());
            $this->entityManager->persist($coin);
            $this->entityManager->remove($insertedCoin);
        }
        $this->entityManager->flush();
    }

    private function decreaseProductNumber(Product $product):void
    {
        $product->setQuantity($product->getQuantity() - 1);
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }
} 
