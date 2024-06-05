<?php declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Application\Command;

use App\BikeRides\Rides\Application\Command\SummariseRide\SummariseRideCommand;
use App\BikeRides\Rides\Application\Command\SummariseRide\SummariseRideHandler;
use App\BikeRides\Rides\Domain\Model\Ride\Route;
use App\BikeRides\Shared\Domain\Model\BikeId;
use App\BikeRides\Shared\Domain\Model\RideId;
use App\BikeRides\Shared\Domain\Model\RiderId;
use App\Foundation\Location;
use App\Tests\BikeRides\Rides\Doubles\RouteFetcherStub;

final class SummariseRideTest extends CommandTestCase
{
    private SummariseRideHandler $handler;
    private RouteFetcherStub $routeFetcher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = new SummariseRideHandler(
            $this->rideRepository,
            $this->routeFetcher = new RouteFetcherStub(),
        );
    }

    public function test_it_summarises_a_ride(): void
    {
        $this->createRider($riderId = RiderId::fromString('rider_id'));
        $this->createBike($bikeId = BikeId::generate());
        $this->startRide($rideId = RideId::generate(), $riderId, $bikeId);
        $this->endRide($rideId);

        $route = new Route([new Location(0, 0), new Location(1, 1), new Location(2, 2)]);
        $this->routeFetcher->useRoute($route);

        ($this->handler)(new SummariseRideCommand($rideId->toString()));

        $ride = $this->rideRepository->getById($rideId);

        self::assertTrue($ride->hasBeenSummarised());
        self::assertEquals($route, $ride->getRoute());
    }

    public function test_it_cannot_summarise_a_ride_that_has_already_been_summarised(): void
    {
        $this->createRider($riderId = RiderId::fromString('rider_id'));
        $this->createBike($bikeId = BikeId::generate());
        $this->startRide($rideId = RideId::generate(), $riderId, $bikeId);
        $this->endRide($rideId);

        ($this->handler)(new SummariseRideCommand($rideId->toString()));

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Ride has already been summarised');

        ($this->handler)(new SummariseRideCommand($rideId->toString()));
    }

    public function test_it_cannot_summarise_a_ride_that_has_not_ended(): void
    {
        $this->createRider($riderId = RiderId::fromString('rider_id'));
        $this->createBike($bikeId = BikeId::generate());
        $this->startRide($rideId = RideId::generate(), $riderId, $bikeId);

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Ride has not ended');

        ($this->handler)(new SummariseRideCommand($rideId->toString()));
    }
}
