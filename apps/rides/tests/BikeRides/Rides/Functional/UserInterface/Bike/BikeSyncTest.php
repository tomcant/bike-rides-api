<?php

declare(strict_types=1);

namespace BikeRides\Rides\Functional\UserInterface\Bike;

use App\Tests\BikeRides\Rides\Functional\UserInterface\RidesUserInterfaceTestCase;
use BikeRides\SharedKernel\Domain\Event\BikeActivated;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\Location;

final class BikeSyncTest extends RidesUserInterfaceTestCase
{
    public function test_bike_details_are_synced_when_a_bike_is_activated(): void
    {
        $bikeId = BikeId::generate()->toString();
        $location = new Location(0, 0);

        $this->getJson("/bike/{$bikeId}", assertResponseIsSuccessful: false);
        self::assertResponseStatusCodeSame(404);

        $this->handleDomainEvent(new BikeActivated($bikeId, $location));

        $bike = $this->getJson("/bike/{$bikeId}");
        self::assertSame($bikeId, $bike['bike_id']);
        self::assertEquals($location->toArray(), $bike['location']);
    }

    public function test_bike_details_are_removed_when_a_bike_is_deactivated(): void
    {
        self::markTestSkipped();
    }
}
