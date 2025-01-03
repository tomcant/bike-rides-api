<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Unit\Application\Query;

use App\BikeRides\Bikes\Application\Query\ListTrackingEventsByBikeId;
use App\BikeRides\Bikes\Domain\Model\TrackingEvent\TrackingEvent;
use App\Tests\BikeRides\Bikes\Doubles\InMemoryTrackingEventRepository;
use BikeRides\Foundation\Clock\Clock;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\Location;
use PHPUnit\Framework\TestCase;

final class ListTrackingEventsByBikeIdTest extends TestCase
{
    private ListTrackingEventsByBikeId $query;
    private InMemoryTrackingEventRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->query = new ListTrackingEventsByBikeId(
            $this->repository = new InMemoryTrackingEventRepository(),
        );
    }

    public function test_it_can_list_tracking_events_by_bike_id(): void
    {
        $bikeId = BikeId::generate();
        $this->repository->store(
            $event1 = new TrackingEvent(
                bikeId: $bikeId,
                location: new Location(0, 0),
                trackedAt: new \DateTimeImmutable('-2 minutes'),
            ),
        );
        $this->repository->store(
            $event2 = new TrackingEvent(
                bikeId: $bikeId,
                location: new Location(1, 1),
                trackedAt: new \DateTimeImmutable('-1 minute'),
            ),
        );

        $events = $this->query->query(
            $bikeId->toString(),
            from: new \DateTimeImmutable('-3 minutes'),
            to: Clock::now(),
        );

        self::assertCount(2, $events);
        self::assertEquals($event1->location->toArray(), $events[0]['location']);
        self::assertEquals($event2->location->toArray(), $events[1]['location']);
        self::assertEquals($event1->trackedAt, $events[0]['trackedAt']);
        self::assertEquals($event2->trackedAt, $events[1]['trackedAt']);
    }

    public function test_no_tracking_events_are_found_when_given_an_unknown_bike_id(): void
    {
        $events = $this->query->query(
            BikeId::generate()->toString(),
            from: new \DateTimeImmutable('-1 minute'),
            to: Clock::now(),
        );

        self::assertSame([], $events);
    }
}
