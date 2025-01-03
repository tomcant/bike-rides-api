<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Billing\Integration\Infrastructure;

use App\BikeRides\Billing\Domain\Model\RidePayment\Event\RidePaymentEventFactory;
use App\BikeRides\Billing\Domain\Model\RidePayment\RideDetails;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePayment;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentId;
use App\BikeRides\Billing\Infrastructure\PostgresEventStore;
use App\Tests\BikeRides\Billing\Doubles\RidePaymentGatewayStub;
use App\Tests\BikeRides\Shared\Doubles\AggregateEventsBusSpy;
use BikeRides\Foundation\Clock\Clock;
use BikeRides\Foundation\Domain\AggregateEvents;
use BikeRides\SharedKernel\Domain\Model\RideDuration;
use BikeRides\SharedKernel\Domain\Model\RideId;
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
            new RidePaymentEventFactory(),
            $this->aggregateEventsBus = new AggregateEventsBusSpy(),
            dbTableName: 'billing.event_store',
        );
    }

    public function test_it_persists_and_hydrates_aggregate(): void
    {
        [$ridePayment] = $this->buildRidePayment();

        $hydratedAggregate = RidePayment::buildFrom(
            $this->eventStore->get($ridePayment->getAggregateName(), $ridePayment->getAggregateId()),
        );

        self::assertEquals($ridePayment, $hydratedAggregate);
    }

    public function test_it_publishes_stored_events_to_the_bus(): void
    {
        [, $events] = $this->buildRidePayment();

        self::assertEquals($events, $this->aggregateEventsBus->getLastEvents());
    }

    public function test_it_throws_unique_constraint_violation_if_aggregate_with_duplicate_version_attempted_to_be_persisted(): void
    {
        [, $events] = $this->buildRidePayment();

        $this->expectException(UniqueConstraintViolationException::class);

        $this->eventStore->store($events);
    }

    /** @return array{RidePayment, AggregateEvents} */
    private function buildRidePayment(): array
    {
        $ridePayment = RidePayment::initiate(
            RidePaymentId::generate(),
            RideId::generate(),
            new RideDetails(
                RideDuration::fromStartAndEnd(
                    ($endedAt = Clock::now())->modify('-1 minute'),
                    $endedAt,
                ),
            ),
        );
        $ridePayment->capture(new RidePaymentGatewayStub('external_payment_ref'));

        $events = $ridePayment->flushEvents();

        $this->eventStore->store($events);

        return [$ridePayment, $events];
    }
}
