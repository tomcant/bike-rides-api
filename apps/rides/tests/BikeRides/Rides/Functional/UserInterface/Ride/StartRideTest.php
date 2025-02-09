<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Functional\UserInterface\Ride;

use App\Tests\BikeRides\Rides\Functional\UserInterface\RidesUserInterfaceTestCase;

final class StartRideTest extends RidesUserInterfaceTestCase
{
    public function test_starting_a_ride(): void
    {
        $rider = $this->createRider();
        $bike = $this->createBike();

        ['ride_id' => $rideId] = $this->postJson(
            $bike['_links']['start-ride']['href'],
            ['rider_id' => $rider['rider_id']],
        );

        self::assertNotNull($rideId);
        self::assertResponseStatusCodeSame(201);

        $rideUrl = $this->parseResponseLinkUrl();
        self::assertStringContainsString($rideId, $rideUrl);

        $ride = $this->getJson($rideUrl);
        self::assertSame($rideId, $ride['ride_id']);
        self::assertIsNumeric($ride['started_at']);
        self::assertNull($ride['ended_at']);
    }

    public function test_multiple_rides_cannot_be_started_on_the_same_bike_at_the_same_time(): void
    {
        $rider = $this->createRider();
        $bike = $this->createBike();
        $this->startRide($rider['rider_id'], $bike['bike_id']);

        $response = $this->postJson(
            $bike['_links']['start-ride']['href'],
            ['rider_id' => $rider['rider_id']],
            assertResponseIsSuccessful: false,
        );

        self::assertResponseStatusCodeSame(400);
        self::assertSame("Bike '{$bike['bike_id']}' is not available", $response['detail']);
    }
}
