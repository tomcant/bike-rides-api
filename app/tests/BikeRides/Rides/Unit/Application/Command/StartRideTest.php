<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Unit\Application\Command;

use App\BikeRides\Rides\Application\Command\StartRide\StartRideCommand;
use App\BikeRides\Rides\Application\Command\StartRide\StartRideHandler;
use App\BikeRides\Shared\Domain\Model\BikeId;
use App\BikeRides\Shared\Domain\Model\RideId;
use App\BikeRides\Shared\Domain\Model\RiderId;
use App\Tests\BikeRides\Rides\Doubles\BikeAvailabilityCheckerStub;

final class StartRideTest extends CommandTestCase
{
    public function test_it_starts_a_ride(): void
    {
        $rideId = RideId::generate();
        $this->createRider($riderId = RiderId::fromString('rider_id'));
        $this->createBike($bikeId = BikeId::generate());

        $handler = new StartRideHandler($this->rideRepository, BikeAvailabilityCheckerStub::available());
        $handler(new StartRideCommand($rideId->toString(), $riderId->toString(), $bikeId->toString()));

        self::assertObjectEquals($rideId, $this->rideRepository->getById($rideId)->getAggregateId());
    }

    public function test_it_cannot_start_a_ride_if_the_bike_is_not_available(): void
    {
        $rideId = RideId::generate();
        $this->createRider($riderId = RiderId::fromString('rider_id'));
        $this->createBike($bikeId = BikeId::generate());

        self::expectException(\DomainException::class);
        self::expectExceptionMessage(\sprintf('Bike "%s" is not available', $bikeId->toString()));

        $handler = new StartRideHandler($this->rideRepository, BikeAvailabilityCheckerStub::notAvailable());
        $handler(new StartRideCommand($rideId->toString(), $riderId->toString(), $bikeId->toString()));
    }
}
