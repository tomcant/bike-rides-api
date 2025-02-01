<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Functional\UserInterface\Bike;

use App\Tests\BikeRides\Bikes\Functional\UserInterface\BikesUserInterfaceTestCase;
use BikeRides\SharedKernel\Domain\Model\Location;

final class BikeDeactivationTest extends BikesUserInterfaceTestCase
{
    public function test_deactivating_a_bike(): void
    {
        $bike = $this->registerBike();
        $this->recordTrackingEvent($bike['bike_id'], new Location(0, 0));
        $this->activateBike($bike['bike_id']);
        $bike = $this->retrieveBike($bike['bike_id']);

        $this->postJson($bike['_links']['deactivate']['href']);

        $bike = $this->retrieveBike($bike['bike_id']);
        self::assertArrayNotHasKey('deactivate', $bike['_links']);
        self::assertFalse($bike['is_active']);
    }
}
