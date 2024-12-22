<?php

declare(strict_types=1);

namespace App\BikeRides\Shared\Infrastructure;

use BikeRides\Foundation\Domain\DomainEvent;
use BikeRides\Foundation\Domain\DomainEventBus;
use BikeRides\Foundation\Timestamp;
use Doctrine\DBAL\Connection;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class SymfonyDomainEventBus implements DomainEventBus
{
    public function __construct(
        #[Autowire(service: 'domain_event.bus')]
        private MessageBusInterface $domainEventBus,
        private Connection $connection,
    ) {
    }

    public function publish(DomainEvent $event): void
    {
        $this->log($event);
        $this->domainEventBus->dispatch($event);
    }

    private function log(DomainEvent $event): void
    {
        $this->connection->executeStatement(
            '
                INSERT INTO public.domain_event_log (event_name, event_data, occurred_at)
                VALUES (:eventName, :eventData, :occurredAt)
            ',
            [
                'eventName' => $event::class,
                'eventData' => $event->serialize(),
                'occurredAt' => Timestamp::format($event->occurredAt),
            ],
        );
    }
}
