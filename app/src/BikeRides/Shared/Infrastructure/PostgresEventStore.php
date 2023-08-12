<?php declare(strict_types=1);

namespace App\BikeRides\Shared\Infrastructure;

use App\BikeRides\Shared\Domain\Helpers\AggregateEvent;
use App\BikeRides\Shared\Domain\Helpers\AggregateEventFactory;
use App\BikeRides\Shared\Domain\Helpers\AggregateEvents;
use App\BikeRides\Shared\Domain\Helpers\AggregateEventsBus;
use App\BikeRides\Shared\Domain\Helpers\AggregateName;
use App\BikeRides\Shared\Domain\Helpers\EntityId;
use App\BikeRides\Shared\Domain\Helpers\EventStore;
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
            $statement->executeStatement([
                'aggregate_name' => $event->getAggregateName()->toString(),
                'aggregate_id' => $event->getAggregateId()->toString(),
                'aggregate_version' => $event->getAggregateVersion()->toInt(),
                'event_name' => $event->getEventName(),
                'event_data' => $event->serialize(),
            ]);
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

        $result = $statement->executeQuery([
            'aggregate_name' => $name->toString(),
            'aggregate_id' => $id->toString(),
        ]);

        return \array_reduce(
            $result->fetchAllAssociative(),
            fn (AggregateEvents $events, array $event)
                => $events->add(
                    $this->aggregateEventFactory->fromSerialized(
                        $event['event_name'],
                        $event['event_data'],
                    ),
                ),
            AggregateEvents::make(),
        );
    }
}
