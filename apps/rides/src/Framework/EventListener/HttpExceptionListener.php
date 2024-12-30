<?php

declare(strict_types=1);

namespace App\Framework\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final readonly class HttpExceptionListener
{
    #[AsEventListener(event: KernelEvents::EXCEPTION)]
    public function onHttpException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $response = \array_filter([
            'title' => Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR],
            'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'detail' => $exception->getMessage(),
        ]);

        if ($exception instanceof HttpExceptionInterface) {
            $response['title'] = Response::$statusTexts[$exception->getStatusCode()] ?? 'Internal Server Error';
            $response['status'] = $exception->getStatusCode();
        }

        $event->setResponse(
            new JsonResponse(
                data: $response,
                status: $response['status'],
                headers: ['Content-Type' => 'application/problem+json'],
            ),
        );
    }
}
