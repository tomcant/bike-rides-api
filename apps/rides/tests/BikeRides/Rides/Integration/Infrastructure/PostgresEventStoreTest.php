<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Integration\Infrastructure;

use App\BikeRides\Rides\Domain\Model\Ride\Event\RideEventFactory;
use App\BikeRides\Rides\Domain\Model\Ride\Ride;
use App\BikeRides\Rides\Infrastructure\PostgresEventStore;
use App\Tests\BikeRides\Shared\Doubles\AggregateEventsBusSpy;
use BikeRides\Foundation\Domain\AggregateEvents;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\RideId;
use BikeRides\SharedKernel\Domain\Model\RiderId;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

final class PostgresEventStoreTest extends PostgresTestCase
{
    private PostgresEventStore $eventStore;
    private AggregateEventsBusSpy $aggregateEventsBus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->eventStore = new PostgresEventStore(
            $this->connection,
            new RideEventFactory(),
            $this->aggregateEventsBus = new AggregateEventsBusSpy(),
            dbTableName: 'rides.event_store',
        );
    }

    public function test_it_persists_and_hydrates_aggregate(): void
    {
        [$ride] = $this->buildRide();

        $hydratedAggregate = Ride::buildFrom(
            $this->eventStore->get($ride->getAggregateName(), $ride->getAggregateId()),
        );

        self::assertEquals($ride, $hydratedAggregate);
    }

    public function test_it_publishes_stored_events_to_the_bus(): void
    {
        [, $events] = $this->buildRide();

        self::assertEquals($events, $this->aggregateEventsBus->getLastEvents());
    }

    public function test_it_throws_unique_constraint_violation_if_aggregate_with_duplicate_version_attempted_to_be_persisted(): void
    {
        [, $events] = $this->buildRide();

        $this->expectException(UniqueConstraintViolationException::class);

        $this->eventStore->store($events);
    }

    /** @return array{Ride, AggregateEvents} */
    private function buildRide(): array
    {
        $ride = Ride::start(
            RideId::generate(),
            RiderId::fromString('rider_id'),
            BikeId::generate(),
        );
        $ride->end();

        $events = $ride->flushEvents();

        $this->eventStore->store($events);

        return [$ride, $events];
    }
}
