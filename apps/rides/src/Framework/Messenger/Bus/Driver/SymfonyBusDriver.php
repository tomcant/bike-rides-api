<?php

declare(strict_types=1);

namespace App\Framework\Messenger\Bus\Driver;

use Bref\Symfony\Messenger\Service\BusDriver;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\ConsumedByWorkerStamp;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/*
 * This allows us to use the Symfony Messenger built-in failure strategies.
 */
final readonly class SymfonyBusDriver implements BusDriver
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private LoggerInterface $logger,
    ) {
    }

    public function putEnvelopeOnBus(MessageBusInterface $bus, Envelope $envelope, string $transportName): void
    {
        $event = new WorkerMessageReceivedEvent($envelope, $transportName);
        $this->eventDispatcher->dispatch($event);

        if ($event->shouldHandle() === false) {
            return;
        }

        try {
            $envelope = $bus->dispatch($envelope->with(new ReceivedStamp($transportName), new ConsumedByWorkerStamp()));
        } catch (\Throwable $throwable) {
            if ($throwable instanceof HandlerFailedException) {
                $envelope = $throwable->getEnvelope();
            }

            $this->logger->warning($throwable);

            $this->eventDispatcher->dispatch(new WorkerMessageFailedEvent($envelope, $transportName, $throwable));

            return;
        }

        $this->eventDispatcher->dispatch(new WorkerMessageHandledEvent($envelope, $transportName));

        $this->logger->debug('{class} was handled successfully (acknowledging to transport).', [
            'class' => $envelope->getMessage()::class,
            'message' => $envelope->getMessage(),
            'transport' => $transportName,
        ]);
    }
}
