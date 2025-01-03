<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Functional\UserInterface\Bike;

use App\Tests\BikeRides\Rides\Functional\UserInterface\RidesUserInterfaceTestCase;

final class BikeListingTest extends RidesUserInterfaceTestCase
{
    public function test_listing_bikes(): void
    {
        $bike1 = $this->createBike();
        $bike2 = $this->createBike();

        $list = $this->getJson('/bike');

        self::assertSame(2, $list['total']);
        self::assertCount(2, $list['_links']['bike']);
        self::assertCount(2, $list['_embedded']['bike']);
        self::assertContainsEquals($bike1, $list['_embedded']['bike']);
        self::assertContainsEquals($bike2, $list['_embedded']['bike']);
    }
}
