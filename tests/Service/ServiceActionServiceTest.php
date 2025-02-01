<?php

use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\ServiceActionService;
use App\Entity\Coin;
use App\Entity\Product;
use App\Repository\CoinRepository;
use App\Repository\ProductRepository;

class ServiceActionServiceTest extends TestCase
{
    private $entityManager;
    private ServiceActionService $serviceActionService;
    private $coinRepository;
    private $productRepository;

    protected function setUp(): void
    {
        
        $this->coinRepository = $this->createMock(CoinRepository::class);
        $this->productRepository = $this->createMock(ProductRepository::class);

      
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        
        $this->entityManager->method('getRepository')
            ->willReturnMap([
                [Coin::class, $this->coinRepository],
                [Product::class, $this->productRepository],
            ]);

       
        $this->serviceActionService = new ServiceActionService($this->entityManager);
    }

    public function testPersistAndFlush()
    {
        
        $this->entityManager->expects($this->exactly(2))  
            ->method('persist');   
        
        $this->entityManager->expects($this->once())  
            ->method('flush');

        
        $data = [
            'change' => [
                ['value' => 25, 'quantity' => 5],
                ['value' => 10, 'quantity' => 3]
            ]
        ];

        
        $this->serviceActionService->__invoke($data);
    }

    public function testServiceActionWithItems()
    {
        
        $productMock = $this->createMock(Product::class);
        $this->productRepository->expects($this->exactly(2)) 
            ->method('findOneBy')
            ->willReturn($productMock);  

        
        $this->entityManager->expects($this->exactly(2))  
            ->method('persist')
            ->with($this->isInstanceOf(Product::class));  

        $this->entityManager->expects($this->once()) 
            ->method('flush');

        
        $data = [
            'items' => [
                ['name' => 'SODA', 'price' => 150, 'quantity' => 10],
                ['name' => 'WATER', 'price' => 65, 'quantity' => 5],
            ]
        ];

        
        $this->serviceActionService->__invoke($data);
    }
}
