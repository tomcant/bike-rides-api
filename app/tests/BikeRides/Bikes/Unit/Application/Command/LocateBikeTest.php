<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Unit\Application\Command;

use App\BikeRides\Bikes\Application\Command\LocateBike\LocateBikeCommand;
use App\BikeRides\Bikes\Application\Command\LocateBike\LocateBikeHandler;
use App\BikeRides\Shared\Domain\Event\BikeLocated;
use App\BikeRides\Shared\Domain\Model\BikeId;
use App\Foundation\Location;
use App\Tests\BikeRides\Shared\Doubles\DomainEventBusSpy;

final class LocateBikeTest extends CommandTestCase
{
    public function test_it_updates_bike_location(): void
    {
        $this->registerBike($bikeId = BikeId::generate());
        $this->activateBike($bikeId, $location = new Location(0, 0));
        $locatedAt = new \DateTimeImmutable('now');

        $handler = new LocateBikeHandler($this->bikeLocationRepository, $eventBus = new DomainEventBusSpy());
        $handler(new LocateBikeCommand($bikeId->toString(), $location, $locatedAt));

        $bikeLocations = $this->bikeLocationRepository->getBetweenForBikeId(
            $locatedAt->modify('-1 minute'),
            $locatedAt->modify('+1 minute'),
            $bikeId,
        );

        self::assertCount(1, $bikeLocations);
        self::assertEquals($bikeId, $bikeLocations[0]->bikeId);
        self::assertEquals($location, $bikeLocations[0]->location);
        self::assertEquals($locatedAt, $bikeLocations[0]->locatedAt);

        self::assertDomainEventEquals(
            new BikeLocated(
                bikeId: $bikeId->toString(),
                location: $location,
                locatedAt: $locatedAt,
            ),
            $eventBus->lastEvent,
        );
    }
}
