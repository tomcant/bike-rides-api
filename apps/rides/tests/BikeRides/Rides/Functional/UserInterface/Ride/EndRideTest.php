<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Functional\UserInterface\Ride;

use App\BikeRides\Rides\Application\Command\RefreshBikeLocation\BikeLocationFetcher;
use App\BikeRides\Rides\Application\Command\SummariseRide\RouteFetcher;
use App\BikeRides\Rides\Domain\Model\Summary\Route;
use App\Tests\BikeRides\Rides\Functional\UserInterface\RidesUserInterfaceTestCase;
use BikeRides\SharedKernel\Domain\Event\BikeDeactivated;
use BikeRides\SharedKernel\Domain\Model\Location;

final class EndRideTest extends RidesUserInterfaceTestCase
{
    public function test_ending_a_ride(): void
    {
        $rider = $this->createRider();
        $bike = $this->createBike();
        $ride = $this->startRide($rider['rider_id'], $bike['bike_id']);

        $this->postJson($ride['_links']['end']['href']);

        $ride = $this->retrieveRide($ride['ride_id']);
        self::assertGreaterThan($ride['started_at'], $ride['ended_at']);
    }

    public function test_ending_a_ride_when_the_bike_is_no_longer_active(): void
    {
        $rider = $this->createRider();
        $bike = $this->createBike();
        $ride = $this->startRide($rider['rider_id'], $bike['bike_id']);
        $this->handleDomainEvent(new BikeDeactivated($bike['bike_id']));

        $this->postJson($ride['_links']['end']['href']);

        $ride = $this->retrieveRide($ride['ride_id']);
        self::assertNotNull($ride['ended_at']);
    }

    public function test_the_bike_location_is_refreshed_when_a_ride_ends(): void
    {
        $location = new Location(1, 1);
        self::getContainer()->get(BikeLocationFetcher::class)->useLocation($location);

        $rider = $this->createRider();
        $bike = $this->createBike();
        $ride = $this->startRide($rider['rider_id'], $bike['bike_id']);

        $this->postJson($ride['_links']['end']['href']);

        $bike = $this->retrieveBike($bike['bike_id']);
        self::assertEquals($location->toArray(), $bike['location']);
    }

    public function test_the_summary_is_generated_when_the_ride_ends(): void
    {
        $route = new Route([new Location(0, 0), new Location(1, 1), new Location(2, 2)]);
        self::getContainer()->get(RouteFetcher::class)->useRoute($route);

        $rider = $this->createRider();
        $bike = $this->createBike();
        $ride = $this->startRide($rider['rider_id'], $bike['bike_id']);

        self::assertArrayNotHasKey('summary', $ride['_links']);

        $this->postJson($ride['_links']['end']['href']);

        $ride = $this->retrieveRide($ride['ride_id']);
        self::assertArrayHasKey('summary', $ride['_links']);

        $summary = $this->getJson($ride['_links']['summary']['href']);

        self::assertArrayHasKeys($summary, ['_links', 'ride_id', 'duration', 'route']);

        self::assertSame($ride['ride_id'], $summary['ride_id']);
        self::assertEquals($route->toArray(), $summary['route']);
        self::assertGreaterThan($summary['duration']['started_at'], $summary['duration']['ended_at']);
    }
}
