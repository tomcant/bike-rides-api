<?php declare(strict_types=1);

namespace App\Tests\BikeRides\Shared\Infrastructure;

use App\BikeRides\Rides\Domain\Model\Ride\Event\RideEventFactory;
use App\BikeRides\Rides\Domain\Model\Ride\Ride;
use App\BikeRides\Rides\Domain\Model\Shared\BikeId;
use App\BikeRides\Rides\Domain\Model\Shared\RideId;
use App\BikeRides\Rides\Domain\Model\Shared\RiderId;
use App\BikeRides\Shared\Infrastructure\PostgresEventStore;
use App\Tests\BikeRides\Rides\Doubles\BikeAvailabilityCheckerStub;
use App\Tests\BikeRides\Shared\Doubles\AggregateEventsBusSpy;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

final class PostgresEventStoreTest extends PostgresTestCase
{
    private const EVENT_STORE_TABLE_NAME = 'rides.event_store';

    private PostgresEventStore $eventStore;
    private AggregateEventsBusSpy $aggregateEventsBus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->eventStore = new PostgresEventStore(
            $this->connection,
            new RideEventFactory(),
            $this->aggregateEventsBus = new AggregateEventsBusSpy(),
            self::EVENT_STORE_TABLE_NAME,
        );

        $this->connection->executeQuery('TRUNCATE TABLE ' . self::EVENT_STORE_TABLE_NAME);
    }

    public function test_it_persists_and_hydrates_aggregate(): void
    {
        [$ride] = $this->startRide();

        $hydratedAggregate = Ride::buildFrom(
            $this->eventStore->get($ride->getAggregateName(), $ride->getAggregateId()),
        );

        self::assertEquals($ride, $hydratedAggregate);
    }

    public function test_it_publishes_stored_events_to_the_bus(): void
    {
        [, $events] = $this->startRide();

        self::assertEquals($events, $this->aggregateEventsBus->getLastEvents());
    }

    public function test_it_throws_unique_constraint_violation_if_aggregate_with_duplicate_version_attempted_to_be_persisted(): void
    {
        [, $events] = $this->startRide();

        $this->expectException(UniqueConstraintViolationException::class);

        $this->eventStore->store($events);
    }

    private function startRide(): array
    {
        $ride = Ride::start(
            RideId::generate(),
            RiderId::fromString('rider_id'),
            BikeId::generate(),
            BikeAvailabilityCheckerStub::available(),
        );

        $events = $ride->flushEvents();

        $this->eventStore->store($events);

        return [$ride, $events];
    }
}
