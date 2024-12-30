<?php

declare(strict_types=1);

namespace App\Framework\Messenger\Middleware;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

final readonly class SendToTransportMiddleware implements MiddlewareInterface
{
    public function __construct(private TransportInterface $transport)
    {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        return $this->transport->send($envelope);
    }
}
