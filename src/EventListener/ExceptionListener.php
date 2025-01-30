<?php

namespace App\EventListener;

use App\Exceptions\VendingException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener
{
    public function __construct(private LoggerInterface $logger){}

    public function onKernelException(ExceptionEvent $event): void
    {
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
        }

       
        $response = new JsonResponse([
            'error' => $message,
        ], $statusCode);

    
        $event->setResponse($response);
    }
}