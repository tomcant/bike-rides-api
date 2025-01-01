<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Functional\UserInterface\Ride;

use App\Tests\BikeRides\Rides\Functional\UserInterface\RidesUserInterfaceTestCase;

final class RideRetrievalTest extends RidesUserInterfaceTestCase
{
    public function test_retrieving_a_ride(): void
    {
        $rider = $this->createRider();
        $bike = $this->createBike();
        $ride = $this->startRide($rider['rider_id'], $bike['bike_id']);

        $response = $this->getJson("/ride/{$ride['ride_id']}");

        self::assertArrayHasKeys($response, ['_links', 'ride_id', 'rider_id', 'bike_id', 'started_at', 'ended_at']);
        self::assertArrayHasKeys($response['_links'], ['self', 'end']);

        self::assertSame($ride['ride_id'], $response['ride_id']);
        self::assertSame($rider['rider_id'], $response['rider_id']);
        self::assertSame($bike['bike_id'], $response['bike_id']);
    }
}
