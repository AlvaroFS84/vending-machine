<?php

namespace App\Tests\Service;

use App\Entity\InsertedCoin;
use App\Entity\Coin;
use App\Enum\CurrencyValue;
use App\Repository\InsertedCoinRepository;
use App\Service\ReturnMoneyService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ReturnMoneyServiceTest extends TestCase
{
    private MockObject $entityManager;
    private MockObject $insertedCoinRepository;
    private ReturnMoneyService $returnMoneyService;

    protected function setUp(): void
    {
        
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->insertedCoinRepository = $this->createMock(InsertedCoinRepository::class);

        $this->entityManager->method('getRepository')
            ->willReturn($this->insertedCoinRepository);

        // Creamos el servicio
        $this->returnMoneyService = new ReturnMoneyService($this->entityManager);
    }

    public function testReturnMoneyService(): void
    {
        
        $coinMock = $this->createMock(Coin::class);
        $coinMock->method('getValue')->willReturn(CurrencyValue::EURO); 

        $coinMockTwo = $this->createMock(Coin::class);
        $coinMockTwo->method('getValue')->willReturn(CurrencyValue::QUARTER); 
        
        $insertedCoinMock = $this->createMock(InsertedCoin::class);
        $insertedCoinMock->method('getCoinId')->willReturn($coinMock);
        $insertedCoinMock->method('getQuantity')->willReturn(2);
        
        $insertedCoinMockTwo = $this->createMock(InsertedCoin::class);
        $insertedCoinMockTwo->method('getCoinId')->willReturn($coinMockTwo);
        $insertedCoinMockTwo->method('getQuantity')->willReturn(1); 

        $this->insertedCoinRepository->method('findAll')->willReturn([$insertedCoinMock, $insertedCoinMockTwo]);

        $this->insertedCoinRepository->expects($this->once())->method('deleteAll');

        $result = $this->returnMoneyService->__invoke();

        $this->assertCount(3, $result); 
        $this->assertEquals('1.00', $result[0]);
        $this->assertEquals('1.00', $result[1]);
        $this->assertEquals('0.25', $result[2]);
    }
}
