<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Unit\Application\Command;

use App\BikeRides\Bikes\Application\Command\RecordTrackingEvent\RecordTrackingEventCommand;
use App\BikeRides\Bikes\Application\Command\RecordTrackingEvent\RecordTrackingEventHandler;
use BikeRides\Foundation\Clock\Clock;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\Location;

final class RecordTrackingEventTest extends CommandTestCase
{
    public function test_it_records_a_tracking_event(): void
    {
        $this->registerBike($bikeId = BikeId::generate());
        $location = new Location(0, 0);
        $trackedAt = Clock::now();

        $handler = new RecordTrackingEventHandler($this->trackingEventRepository);
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
    }
}
