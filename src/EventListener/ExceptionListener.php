<?php

namespace App\EventListener;

use App\Exceptions\VendingException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelInterface;

class ExceptionListener
{
    public function __construct(
        private LoggerInterface $logger,
        private KernelInterface $kernel
    ) {}

    public function onKernelException(ExceptionEvent $event): void
    {
        $environment = $this->kernel->getEnvironment();

        $exception = $event->getThrowable();
        $statusCode = 200;
        $message = "There is something wrong, please try again later";

        $this->logger->error('Exception caught: ' . $exception->getMessage(), [
            'exception' => $exception,
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);
    
        if ($exception instanceof VendingException) {
            $statusCode = $exception->getCode();
            $message = $exception->getMessage();
        }else if ($environment === 'dev') {
            $message = $exception->getMessage();
            $statusCode = $exception->getCode() ?: 500;
        }


       
        $response = new JsonResponse([
            'error' => $message,
        ], $statusCode);

    
        $event->setResponse($response);
    }
}