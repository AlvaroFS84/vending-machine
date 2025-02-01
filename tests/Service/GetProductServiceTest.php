<?php

namespace App\Tests\Service;

use App\Entity\Coin;
use App\Entity\InsertedCoin;
use App\Entity\Product;
use App\Enum\CurrencyValue;
use App\Enum\ProductName;
use App\Exceptions\NonExistingProductException;
use App\Repository\CoinRepository;
use App\Repository\InsertedCoinRepository;
use App\Repository\ProductRepository;
use App\Service\CoinService;
use App\Service\GetProductService;
use App\Service\ProductService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class GetProductServiceTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private GetProductService $service;
    private ProductService $productService;
    private CoinService $coinService;
    private ProductRepository $productRepository;
    private InsertedCoinRepository $insertedCoinRepository;
    private CoinRepository $coinRepository;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->productService = $this->createMock(ProductService::class);
        $this->coinService = $this->createMock(CoinService::class);
        $this->service = new GetProductService($this->entityManager, $this->productService, $this->coinService);
        $this->productRepository = $this->createMock(ProductRepository::class);
        $this->insertedCoinRepository = $this->createMock(InsertedCoinRepository::class);
        $this->coinRepository = $this->createMock(CoinRepository::class);
    }

    public function testGetProductExistingAndInStockWithNotEnoughInsertedCoins(): void
    {
        
        $product = $this->createMock(Product::class);
        $product->method('getName')->willReturn(ProductName::SODA);
        $product->method('getPrice')->willReturn(100);
        $product->method('getQuantity')->willReturn(10);

       
        $this->productRepository->method('findOneBy')->with(['name' => 'SODA'])->willReturn($product);
        $this->entityManager->method('getRepository')->willReturn($this->productRepository);

        $this->entityManager->method('getRepository')->willReturn($this->coinRepository);

        $this->insertedCoinRepository->method('findAll')->willReturn([]);
        $this->entityManager->method('getRepository')->willReturn($this->insertedCoinRepository);

        $response = $this->service->__invoke('GET-SODA');
        
        $this->assertEquals([['message' => 'Please insert more coins']], $response);
    }

    public function testGetProductOutOfStock(): void
    {
        
        $product = $this->createMock(Product::class);
        $product->method('getName')->willReturn(ProductName::SODA);
        $product->method('getQuantity')->willReturn(0);
        
        $this->productRepository->method('findOneBy')->with(['name' => 'SODA'])->willReturn($product);
        
        $this->entityManager->method('getRepository')->willReturn($this->productRepository);
    
        $response = $this->service->__invoke('GET-SODA');
    
        $this->assertEquals(['message' => 'Product is out of stock'], $response);
    }
    

    public function testGetProductNotFound(): void
    {
        $this->productRepository->method('findOneBy')->with(['name' => 'TEA'])->willReturn(null);
        
        $this->expectException(NonExistingProductException::class);

        $response = $this->service->__invoke('product-Soda');
    }

    public function testGetProductWithChange(): void
    {
        $product = $this->createMock(Product::class);
        $product->method('getName')->willReturn(ProductName::SODA);
        $product->method('getPrice')->willReturn(100);
        $product->method('getQuantity')->willReturn(10);

        $insertedCoinOne = $this->createMock(InsertedCoin::class);
        $insertedCoinTwo = $this->createMock(InsertedCoin::class);

        
        $coinForInsertedOne = $this->createMock(Coin::class);
        
        $coinForInsertedOne->method('getValue')->willReturn(CurrencyValue::EURO);
        $insertedCoinOne->method('getCoinId')->willReturn($coinForInsertedOne);
        $insertedCoinOne->method('getQuantity')->willReturn(1);

        
        $coinForInsertedTwo = $this->createMock(Coin::class);
        $coinForInsertedTwo->method('getValue')->willReturn(CurrencyValue::QUARTER);
        $insertedCoinTwo->method('getCoinId')->willReturn($coinForInsertedTwo);
        $insertedCoinTwo->method('getQuantity')->willReturn(1);

        
        $insertedCoins = [$insertedCoinOne, $insertedCoinTwo];

        $changeCoin = $this->createMock(Coin::class);
        $changeCoin->method('getQuantity')->willReturn(1);
        $changeCoin->method('getValue')->willReturn(CurrencyValue::QUARTER);
        
        $coinsForChange = [$changeCoin];

        $this->productRepository->method('findOneBy')->with(['name' => 'SODA'])->willReturn($product);

        $this->insertedCoinRepository->method('findAll')->willReturn($insertedCoins);

        $this->coinRepository->method('findBy')->willReturn($coinsForChange);

        
        $this->entityManager->method('getRepository')
            ->will($this->returnValueMap([
                [Product::class, $this->productRepository],
                [InsertedCoin::class, $this->insertedCoinRepository],
                [Coin::class, $this->coinRepository]
            ]));

       
        $this->coinService->expects($this->once())
            ->method('deleteSelectCoinstoChangeFromAvailableCoins')
            ->with([25]);
        
        $this->coinService->expects($this->once())
            ->method('addInsertedCoinsToAvaliableCoins')
            ->with($insertedCoins);
        
        $this->productService->expects($this->once())
            ->method('decreaseProductNumber')
            ->with($product);

       
        $response = $this->service->__invoke('GET-SODA');

        $expected = [['message' => [ProductName::SODA, '0.25']]];
        $this->assertEquals($expected, $response);
    }

}
