<?php declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Application\Command;

use App\BikeRides\Rides\Application\Command\TrackBike\TrackBikeCommand;
use App\BikeRides\Rides\Application\Command\TrackBike\TrackBikeHandler;
use App\BikeRides\Rides\Domain\Model\Shared\BikeId;
use App\BikeRides\Shared\Domain\Event\BikeTracked;
use App\Foundation\Location;
use App\Tests\BikeRides\Shared\Doubles\DomainEventBusSpy;

final class TrackBikeTest extends CommandTestCase
{
    public function test_it_stores_track_event(): void
    {
        $this->registerBike($bikeId = BikeId::generate());
        $location = new Location(0, 0);
        $trackedAt = new \DateTimeImmutable();

        $handler = new TrackBikeHandler($this->trackRepository, $eventBus = new DomainEventBusSpy());
        $handler(new TrackBikeCommand($bikeId->toString(), $location, $trackedAt));

        $tracks = $this->trackRepository->getBetweenForBikeId(
            $trackedAt->modify('-1 minute'),
            $trackedAt->modify('+1 minute'),
            $bikeId,
        );

        self::assertCount(1, $tracks);
        self::assertEquals($bikeId, $tracks[0]->bikeId);
        self::assertEquals($location, $tracks[0]->location);
        self::assertEquals($trackedAt, $tracks[0]->trackedAt);

        $lastDomainEvent = $eventBus->lastEvent;
        self::assertInstanceOf(BikeTracked::class, $lastDomainEvent);
        self::assertSame($bikeId->toString(), $lastDomainEvent->bikeId);
        self::assertEquals($location, $lastDomainEvent->location);
        self::assertEquals($trackedAt, $lastDomainEvent->trackedAt);
    }
}
