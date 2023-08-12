<?php declare(strict_types=1);

namespace App\BikeRides\Shared\Infrastructure;

use App\BikeRides\Shared\Domain\Helpers\DomainEvent;
use App\BikeRides\Shared\Domain\Helpers\DomainEventBus;
use Doctrine\DBAL\Connection;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class SymfonyDomainEventBus implements DomainEventBus
{
    public function __construct(private MessageBusInterface $domainEventBus, private Connection $connection)
    {
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
                'occurredAt' => \datetime_timestamp($event->occurredAt),
            ],
        );
    }
}
