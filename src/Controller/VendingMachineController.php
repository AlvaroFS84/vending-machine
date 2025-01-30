<?php

namespace App\Controller;

use App\Exceptions\BadJsonContentException;
use App\Service\InsertService;
use App\Service\ServiceActionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class VendingMachineController extends AbstractController
{
    public function __construct(
        private ServiceActionService $serviceActionService,
        private InsertService $insertService
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
}
