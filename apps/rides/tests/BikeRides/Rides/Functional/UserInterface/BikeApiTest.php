<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Functional\UserInterface;

use BikeRides\SharedKernel\Domain\Event\BikeActivated;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\Location;

final class BikeApiTest extends RidesUserInterfaceTestCase
{
    public function test_bike_is_created_on_bike_activated_event(): void
    {
        $bikeId = BikeId::generate()->toString();
        $location = new Location(0, 0);

        $this->publishEvent(new BikeActivated($bikeId, $location));

        $bike = $this->retrieveBike($bikeId);
        self::assertSame($bikeId, $bike['bike_id']);
        self::assertEquals($location->toArray(), $bike['location']);
    }

    public function test_retrieve_bike(): void
    {
        ['bike_id' => $bikeId] = $this->createBike();

        $bike = $this->getJson("/rides/bike/{$bikeId}");

        self::assertArrayHasKeys($bike, ['_links', 'bike_id', 'location']);
        self::assertArrayHasKeys($bike['_links'], ['self', 'start-ride']);
        self::assertSame($bikeId, $bike['bike_id']);
    }

    public function test_list_bikes(): void
    {
        $bike1 = $this->createBike();
        $bike2 = $this->createBike();

        $list = $this->getJson('/rides/bike');

        self::assertCount(2, $list['_links']['bike']);
        self::assertCount(2, $list['_embedded']['bike']);
        self::assertSame(2, $list['total']);

        self::assertContainsEquals($bike1, $list['_embedded']['bike']);
        self::assertContainsEquals($bike2, $list['_embedded']['bike']);
    }
}
