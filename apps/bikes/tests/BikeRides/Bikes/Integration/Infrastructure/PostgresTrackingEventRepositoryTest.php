<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Integration\Infrastructure;

use App\BikeRides\Bikes\Domain\Model\Bike\Bike;
use App\BikeRides\Bikes\Domain\Model\TrackingEvent\TrackingEvent;
use App\BikeRides\Bikes\Infrastructure\PostgresBikeRepository;
use App\BikeRides\Bikes\Infrastructure\PostgresTrackingEventRepository;
use BikeRides\Foundation\Clock\Clock;
use BikeRides\Foundation\Domain\CorrelationId;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\Location;

final class PostgresTrackingEventRepositoryTest extends PostgresTestCase
{
    private PostgresTrackingEventRepository $trackingEventRepository;
    private PostgresBikeRepository $bikeRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->trackingEventRepository = new PostgresTrackingEventRepository($this->connection);
        $this->bikeRepository = new PostgresBikeRepository($this->connection);
    }

    public function test_it_stores_a_tracking_event(): void
    {
        $bikeId = $this->registerBike();
        $this->trackingEventRepository->store(
            $event = new TrackingEvent(
                bikeId: $bikeId,
                location: new Location(0, 0),
                trackedAt: Clock::now(),
            ),
        );

        $events = $this->trackingEventRepository->getBetweenForBikeId(
            $bikeId,
            new \DateTimeImmutable('-1 minute'),
            new \DateTimeImmutable('+1 minute'),
        );

        self::assertContainsEquals($event, $events);
    }

    public function test_it_lists_tracking_events_between_timestamps(): void
    {
        $bikeId = $this->registerBike();
        $this->trackingEventRepository->store(
            $event1 = new TrackingEvent(
                bikeId: $bikeId,
                location: new Location(0, 0),
                trackedAt: new \DateTimeImmutable('-5 minutes'),
            ),
        );
        $this->trackingEventRepository->store(
            $event2 = new TrackingEvent(
                bikeId: $bikeId,
                location: new Location(0, 0),
                trackedAt: new \DateTimeImmutable('-3 minutes'),
            ),
        );
        $this->trackingEventRepository->store(
            new TrackingEvent(
                bikeId: $bikeId,
                location: new Location(0, 0),
                trackedAt: new \DateTimeImmutable('-1 minute'),
            ),
        );

        $events = $this->trackingEventRepository->getBetweenForBikeId(
            $bikeId,
            new \DateTimeImmutable('-6 minutes'),
            new \DateTimeImmutable('-2 minutes'),
        );

        self::assertCount(2, $events);
        self::assertContainsEquals($event1, $events);
        self::assertContainsEquals($event2, $events);
    }

    private function registerBike(): BikeId
    {
        $this->bikeRepository->store(Bike::register($correlationId = CorrelationId::generate()));
        $bike = $this->bikeRepository->getByRegistrationCorrelationId($correlationId);

        return $bike->bikeId;
    }
}
