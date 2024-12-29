<?php

declare(strict_types=1);

namespace App\Framework\Messenger\Bus;

use BikeRides\Foundation\Domain\DomainEvent;
use BikeRides\Foundation\Domain\DomainEventBus;
use BikeRides\Foundation\Timestamp;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class SymfonyDomainEventBus implements DomainEventBus
{
    public function __construct(
        #[Autowire(service: 'domain_event.bus')]
        private MessageBusInterface $bus,
        private Connection $connection,
    ) {
    }

    public function publish(DomainEvent $event): void
    {
        $this->log($event);
        $this->bus->dispatch($event);
    }

    private function log(DomainEvent $event): void
    {
        $statement = $this->connection->prepare('
            INSERT INTO public.domain_event_log (id, type, version, data, occurred_at)
            VALUES (:id, :type, :version, :data, :occurred_at)
        ');

        $statement->bindValue('id', $event->id);
        $statement->bindValue('type', $event->type());
        $statement->bindValue('version', $event->version(), ParameterType::INTEGER);
        $statement->bindValue('data', $event->serialize());
        $statement->bindValue('occurred_at', Timestamp::format($event->occurredAt));

        $statement->executeStatement();
    }
}
