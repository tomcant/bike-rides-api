<?php

declare(strict_types=1);

namespace App\BikeRides\Shared\Infrastructure;

use BikeRides\Foundation\Domain\AggregateEvent;
use BikeRides\Foundation\Domain\AggregateEventFactory;
use BikeRides\Foundation\Domain\AggregateEvents;
use BikeRides\Foundation\Domain\AggregateEventsBus;
use BikeRides\Foundation\Domain\AggregateName;
use BikeRides\Foundation\Domain\EntityId;
use BikeRides\Foundation\Domain\EventStore;
use Doctrine\DBAL\Connection;

final readonly class PostgresEventStore implements EventStore
{
    public function __construct(
        private Connection $connection,
        private AggregateEventFactory $aggregateEventFactory,
        private AggregateEventsBus $eventsBus,
        private string $dbTableName,
    ) {
    }

    public function store(AggregateEvents $events): void
    {
        $this->connection->beginTransaction();

        $statement = $this->connection->prepare("
            INSERT INTO {$this->dbTableName} (aggregate_name, aggregate_id, aggregate_version, event_name, event_data)
            VALUES (:aggregate_name, :aggregate_id, :aggregate_version, :event_name, :event_data)
        ");

        /** @var AggregateEvent $event */
        foreach ($events as $event) {
            $statement->bindValue('aggregate_name', $event->getAggregateName()->toString());
            $statement->bindValue('aggregate_id', $event->getAggregateId()->toString());
            $statement->bindValue('aggregate_version', $event->getAggregateVersion()->toInt());
            $statement->bindValue('event_name', $event->getEventName());
            $statement->bindValue('event_data', $event->serialize());

            $statement->executeStatement();
        }

        $this->eventsBus->publish($events);

        $this->connection->commit();
    }

    public function get(AggregateName $name, EntityId $id): AggregateEvents
    {
        $statement = $this->connection->prepare("
            SELECT event_name, event_data
            FROM {$this->dbTableName}
            WHERE aggregate_name = :aggregate_name
            AND aggregate_id = :aggregate_id
            ORDER BY aggregate_version ASC
        ");

        $statement->bindValue('aggregate_name', $name->toString());
        $statement->bindValue('aggregate_id', $id->toString());

        $result = $statement->executeQuery();

        return \array_reduce(
            $result->fetchAllAssociative(),
            fn (AggregateEvents $events, array $event) => $events->add(
                $this->aggregateEventFactory->fromSerialized($event['event_name'], $event['event_data']),
            ),
            AggregateEvents::make(),
        );
    }
}
