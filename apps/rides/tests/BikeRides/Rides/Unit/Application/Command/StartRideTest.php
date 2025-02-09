<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Unit\Application\Command;

use App\BikeRides\Rides\Application\Command\StartRide\StartRideCommand;
use App\BikeRides\Rides\Application\Command\StartRide\StartRideHandler;
use App\Tests\BikeRides\Rides\Doubles\BikeAvailabilityCheckerStub;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\RideId;
use BikeRides\SharedKernel\Domain\Model\RiderId;

final class StartRideTest extends CommandTestCase
{
    public function test_it_starts_a_ride(): void
    {
        $rideId = RideId::generate();
        $this->createRider($riderId = RiderId::fromString('rider_id'));
        $this->createBike($bikeId = BikeId::fromInt(1));

        $handler = new StartRideHandler($this->rideRepository, BikeAvailabilityCheckerStub::available());
        $handler(new StartRideCommand($rideId->toString(), $riderId->toString(), $bikeId->toInt()));

        self::assertObjectEquals($rideId, $this->rideRepository->getById($rideId)->getAggregateId());
    }

    public function test_it_cannot_start_a_ride_if_the_bike_is_not_available(): void
    {
        $rideId = RideId::generate();
        $this->createRider($riderId = RiderId::fromString('rider_id'));
        $this->createBike($bikeId = BikeId::fromInt(1));

        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage("Bike '{$bikeId->toInt()}' is not available");

        $handler = new StartRideHandler($this->rideRepository, BikeAvailabilityCheckerStub::notAvailable());
        $handler(new StartRideCommand($rideId->toString(), $riderId->toString(), $bikeId->toInt()));
    }
}
