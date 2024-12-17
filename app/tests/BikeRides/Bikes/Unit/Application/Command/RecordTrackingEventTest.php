<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Unit\Application\Command;

use App\BikeRides\Bikes\Application\Command\RecordTrackingEvent\RecordTrackingEventCommand;
use App\BikeRides\Bikes\Application\Command\RecordTrackingEvent\RecordTrackingEventHandler;
use App\BikeRides\Shared\Domain\Event\BikeLocated;
use App\BikeRides\Shared\Domain\Model\BikeId;
use App\Foundation\Location;
use App\Tests\BikeRides\Shared\Doubles\DomainEventBusSpy;

final class RecordTrackingEventTest extends CommandTestCase
{
    public function test_it_records_a_tracking_event(): void
    {
        $this->registerBike($bikeId = BikeId::generate());
        $this->activateBike($bikeId, $location = new Location(0, 0));
        $trackedAt = new \DateTimeImmutable('now');

        $handler = new RecordTrackingEventHandler($this->trackingEventRepository, $eventBus = new DomainEventBusSpy());
        $handler(new RecordTrackingEventCommand($bikeId->toString(), $location, $trackedAt));

        $events = $this->trackingEventRepository->getBetweenForBikeId(
            $bikeId,
            $trackedAt->modify('-1 minute'),
            $trackedAt->modify('+1 minute'),
        );
        self::assertCount(1, $events);
        self::assertEquals($bikeId, $events[0]->bikeId);
        self::assertEquals($location, $events[0]->location);
        self::assertEquals($trackedAt, $events[0]->trackedAt);
        self::assertDomainEventEquals(
            new BikeLocated(
                bikeId: $bikeId->toString(),
                location: $location,
                trackedAt: $trackedAt,
            ),
            $eventBus->lastEvent,
        );
    }
}
