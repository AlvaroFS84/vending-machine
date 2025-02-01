<?php

namespace App\Tests\Service;

use App\Entity\Coin;
use App\Entity\InsertedCoin;
use App\Exceptions\NonExistingValueException;
use App\Repository\CoinRepository;
use App\Repository\InsertedCoinRepository;
use App\Service\InsertService;
use Doctrine\ORM\EntityManagerInterface;
use App\Exceptions\VendingException;
use PHPUnit\Framework\TestCase;

class InsertServiceTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private CoinRepository $coinRepository;
    private InsertedCoinRepository $insertedCoinRepository;
    private InsertService $service;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->coinRepository = $this->createMock(CoinRepository::class);
        $this->insertedCoinRepository = $this->createMock(InsertedCoinRepository::class);

        $this->entityManager->method('getRepository')
            ->willReturnMap([
                [Coin::class, $this->coinRepository],
                [InsertedCoin::class, $this->insertedCoinRepository]
            ]);

        $this->service = new InsertService($this->entityManager);
    }

    public function testInsertExistingCoin(): void
    {
        $coin = $this->createMock(Coin::class);
        $insertedCoin = $this->createMock(InsertedCoin::class);

        $this->coinRepository->method('findOneBy')
            ->with(['value' => 100])
            ->willReturn($coin);

        $this->insertedCoinRepository->method('findByCoin')
            ->with($coin)
            ->willReturn($insertedCoin);

        $insertedCoin->method('getQuantity')->willReturn(1);
        $insertedCoin->expects($this->once())->method('setQuantity')->with(2);

        $this->entityManager->expects($this->once())->method('persist')->with($insertedCoin);
        $this->entityManager->expects($this->once())->method('flush');

        // Ejecutar el servicio
        $this->service->__invoke(['value' => 1.00]);
    }

    public function testInsertNewCoin(): void
    {
        $coin = $this->createMock(Coin::class);

        $this->coinRepository->method('findOneBy')
            ->with(['value' => 100])
            ->willReturn($coin);

        $this->insertedCoinRepository->method('findByCoin')
            ->with($coin)
            ->willReturn(null);

        $this->entityManager->expects($this->once())
            ->method('persist');

        $this->entityManager->expects($this->once())->method('flush');

        $this->service->__invoke(['value' => 1.00]);
    }

    public function testInsertNonExistingCoinThrowsException(): void
    {
        $this->coinRepository->method('findOneBy')
            ->with(['value' => 35])
            ->willReturn(null);

        $this->expectException(NonExistingValueException::class);


        $this->service->__invoke(['value' => 0.35]);
    }
}
