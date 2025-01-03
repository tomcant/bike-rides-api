<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Integration\Infrastructure;

use App\BikeRides\Bikes\Domain\Model\TrackingEvent\TrackingEvent;
use App\BikeRides\Bikes\Infrastructure\PostgresTrackingEventRepository;
use BikeRides\Foundation\Clock\Clock;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\Location;

final class PostgresTrackingEventRepositoryTest extends PostgresTestCase
{
    private PostgresTrackingEventRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new PostgresTrackingEventRepository($this->connection);
    }

    public function test_it_stores_a_tracking_event(): void
    {
        $event = new TrackingEvent(
            bikeId: BikeId::generate(),
            location: new Location(0, 0),
            trackedAt: Clock::now(),
        );

        $this->repository->store($event);

        $events = $this->repository->getBetweenForBikeId(
            $event->bikeId,
            new \DateTimeImmutable('-1 minute'),
            new \DateTimeImmutable('+1 minute'),
        );

        self::assertContainsEquals($event, $events);
    }

    public function test_it_lists_tracking_events_between_timestamps(): void
    {
        $bikeId = BikeId::generate();

        $this->repository->store(
            $event1 = new TrackingEvent(
                bikeId: $bikeId,
                location: new Location(0, 0),
                trackedAt: new \DateTimeImmutable('-5 minutes'),
            ),
        );
        $this->repository->store(
            $event2 = new TrackingEvent(
                bikeId: $bikeId,
                location: new Location(0, 0),
                trackedAt: new \DateTimeImmutable('-3 minutes'),
            ),
        );
        $this->repository->store(
            new TrackingEvent(
                bikeId: $bikeId,
                location: new Location(0, 0),
                trackedAt: new \DateTimeImmutable('-1 minute'),
            ),
        );

        $events = $this->repository->getBetweenForBikeId(
            $bikeId,
            new \DateTimeImmutable('-6 minutes'),
            new \DateTimeImmutable('-2 minutes'),
        );

        self::assertCount(2, $events);
        self::assertContainsEquals($event1, $events);
        self::assertContainsEquals($event2, $events);
    }
}
