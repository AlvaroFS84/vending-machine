<?php

namespace App\Controller;

use App\Exceptions\BadJsonContentException;
use App\Exceptions\MalformedProductOrderException;
use App\Exceptions\MissingSelectionException;
use App\Service\GetProductService;
use App\Service\InsertService;
use App\Service\ReturnMoneyService;
use App\Service\ServiceActionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class VendingMachineController extends AbstractController
{
    public function __construct(
        private ServiceActionService $serviceActionService,
        private InsertService $insertService,
        private ReturnMoneyService $returnMoneyService,
        private GetProductService $getProductService
    ){}

    #[Route('/vending/service', name: 'app_vending_service', methods: ['POST'])]
    public function service(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(),true);

        if(!$data){
            throw new BadJsonContentException();
        }

        $this->serviceActionService->__invoke($data);

        return $this->json([
            'message' => 'Service action completed',
        ]);
    }

    #[Route('/vending/insert', name: 'app_vending_insert', methods: ['POST'])]
    public function insert(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(),true);

        if(!$data){
            throw new BadJsonContentException();
        }

        $this->insertService->__invoke($data);

        return $this->json([
            'message' => 'Insert action completed',
        ]);

    }

    #[Route('/vending/return-money', name: 'app_vending_return_money', methods: ['GET'])]
    public function returnMoney(): JsonResponse
    {
        $returned = $this->returnMoneyService->__invoke();
        
        return $this->json($returned);
    }

    #[Route('/vending/select', name: 'app_vending_select', methods: ['GET'])]
    public function selectProduct(Request $request): JsonResponse
    {
        $selection = $request->query->get('selection');

        if(!$selection){
            throw new MissingSelectionException();
        }
        if(!str_starts_with($selection, 'GET-')){
            throw new MalformedProductOrderException();
        }


        $result = $this->getProductService->__invoke($selection);
        
        
        return $this->json($result);
    }
}
