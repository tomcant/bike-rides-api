<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Functional\UserInterface\Bike;

use App\Tests\BikeRides\Bikes\Functional\UserInterface\BikesUserInterfaceTestCase;
use BikeRides\SharedKernel\Domain\Model\Location;

final class BikeListingTest extends BikesUserInterfaceTestCase
{
    public function test_listing_bikes(): void
    {
        $bike1 = $this->registerBike();
        $this->recordTrackingEvent($bike1['bike_id'], new Location(0, 0));
        $this->activateBike($bike1['bike_id']);
        $bike1 = $this->retrieveBike($bike1['bike_id']);
        $bike2 = $this->registerBike();

        $list = $this->getJson('/bike');

        self::assertSame(2, $list['total']);
        self::assertCount(2, $list['_links']['bike']);
        self::assertCount(2, $list['_embedded']['bike']);
        self::assertContainsEquals($bike1, $list['_embedded']['bike']);
        self::assertContainsEquals($bike2, $list['_embedded']['bike']);
    }
}
