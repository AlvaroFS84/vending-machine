<?php

namespace App\Service;

use App\Entity\Coin;
use App\Entity\InsertedCoin;
use App\Entity\Product;
use App\Exceptions\NonExistingProductException;
use App\Repository\InsertedCoinRepository;
use Doctrine\ORM\EntityManagerInterface;

class GetProductService{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductService $productService,
        private CoinService $coinService
    ){}

    public function __invoke(string $selection):array
    {
        $productName = strtoupper(explode('-', $selection)[1]);

        $product = $this->entityManager->getRepository(Product::class)->findOneBy(['name' => $productName]);

        if(!$product){
            throw new NonExistingProductException();
        }

        if($product->getQuantity() === 0){
            return [
                'message' => 'Product is out of stock',
            ];
        }else{
            $result = [];
            $amountInserted = InsertedCoinRepository::getTotalInserted($this->entityManager);
            
            if($amountInserted < $product->getPrice()){
                $result[] = ['message' => 'Please insert more coins'];
            }elseif($amountInserted === $product->getPrice()){
                $insertedCoins = $this->entityManager->getRepository(InsertedCoin::class)->findAll();
                $result[] = ['message' => $product->getName()];
                $this->coinService->addInsertedCoinsToAvaliableCoins($insertedCoins);    
                $this->productService->decreaseProductNumber($product);
            }else{
                $change = $this->calculateChange($product);
                
                if(empty($change)){
                    $result[] = ['message' => 'Cannot provide your change'];
                }else{
                    $insertedCoins = $this->entityManager->getRepository(InsertedCoin::class)->findAll();
                    $this->coinService->deleteSelectCoinstoChangeFromAvailableCoins($change);
                    $this->coinService->addInsertedCoinsToAvaliableCoins($insertedCoins);
                    $this->productService->decreaseProductNumber($product);

                    $change = array_map(fn($value) => number_format($value / 100, 2, '.', ''), $change);
                    $result[] = ['message' => [$product->getName(), ...$change]];
                }
                    
            }
                

            return $result;
        }
    }

    private function calculateChange(Product $product): array
    {
        // Get the amount of change to return
        $totalInserted = InsertedCoinRepository::getTotalInserted($this->entityManager);
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
} 
