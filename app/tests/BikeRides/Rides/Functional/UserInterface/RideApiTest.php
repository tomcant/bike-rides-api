<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Functional\UserInterface;

use App\BikeRides\Rides\Application\Command\RefreshBikeLocation\BikeLocationFetcher;
use App\BikeRides\Rides\Domain\Model\Ride\Route;
use App\BikeRides\Rides\Domain\Model\Ride\RouteFetcher;
use App\Foundation\Location;

final class RideApiTest extends RidesUserInterfaceTestCase
{
    public function test_start_ride(): void
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

    public function test_it_cannot_start_multiple_rides_on_the_same_bike_at_the_same_time(): void
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
        self::assertSame(['error' => \sprintf('Bike "%s" is not available', $bike['bike_id'])], $response);
    }

    public function test_retrieve_ride(): void
    {
        $rider = $this->createRider();
        $bike = $this->createBike();
        $ride = $this->startRide($rider['rider_id'], $bike['bike_id']);

        $response = $this->getJson('/rides/ride/' . $ride['ride_id']);

        self::assertArrayHasKeys($response, ['_links', 'ride_id', 'rider_id', 'bike_id', 'started_at', 'ended_at']);
        self::assertArrayHasKeys($response['_links'], ['self', 'end']);

        self::assertSame($ride['ride_id'], $response['ride_id']);
        self::assertSame($rider['rider_id'], $response['rider_id']);
        self::assertSame($bike['bike_id'], $response['bike_id']);
    }

    public function test_end_ride(): void
    {
        $location = new Location(1, 1);
        self::getContainer()->get(BikeLocationFetcher::class)->useLocation($location);

        $rider = $this->createRider();
        $bike = $this->createBike();
        $ride = $this->startRide($rider['rider_id'], $bike['bike_id']);

        $this->postJson($ride['_links']['end']['href']);

        $ride = $this->retrieveRide($ride['ride_id']);
        self::assertGreaterThan($ride['started_at'], $ride['ended_at']);
    }

    public function test_bike_location_is_refreshed_when_ride_ends(): void
    {
        $location = new Location(1, 1);
        self::getContainer()->get(BikeLocationFetcher::class)->useLocation($location);

        $rider = $this->createRider();
        $bike = $this->createBike();
        $ride = $this->startRide($rider['rider_id'], $bike['bike_id']);
        $this->endRide($ride['ride_id']);

        $bike = $this->retrieveBike($bike['bike_id']);
        self::assertEquals($location->toArray(), $bike['location']);
    }

    public function test_ride_summary_is_generated_when_ride_ends(): void
    {
        $route = new Route([new Location(0, 0), new Location(1, 1), new Location(2, 2)]);
        self::getContainer()->get(RouteFetcher::class)->useRoute($route);

        $rider = $this->createRider();
        $bike = $this->createBike();
        $ride = $this->startRide($rider['rider_id'], $bike['bike_id']);

        self::assertArrayNotHasKey('summary', $ride['_links']);

        $this->endRide($ride['ride_id']);

        $ride = $this->retrieveRide($ride['ride_id']);
        self::assertArrayHasKey('summary', $ride['_links']);

        $summary = $this->getJson($ride['_links']['summary']['href']);

        self::assertArrayHasKeys($summary, ['_links', 'ride_id', 'duration', 'route']);

        self::assertSame($ride['ride_id'], $summary['ride_id']);
        self::assertEquals($route->toArray(), $summary['route']);
        self::assertGreaterThan($summary['duration']['started_at'], $summary['duration']['ended_at']);
    }
}
