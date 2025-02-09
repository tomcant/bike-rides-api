<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Unit\Application\Command;

use App\BikeRides\Rides\Application\Command\RefreshBikeLocation\RefreshBikeLocationCommand;
use App\BikeRides\Rides\Application\Command\RefreshBikeLocation\RefreshBikeLocationHandler;
use App\Tests\BikeRides\Rides\Doubles\BikeLocationFetcherStub;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\Location;
use BikeRides\SharedKernel\Domain\Model\RideId;
use BikeRides\SharedKernel\Domain\Model\RiderId;

final class RefreshBikeLocationTest extends CommandTestCase
{
    public function test_it_refreshes_bike_location(): void
    {
        $this->createRider($riderId = RiderId::fromString('rider_id'));
        $this->createBike($bikeId = BikeId::fromInt(1));
        $this->startRide($rideId = RideId::generate(), $riderId, $bikeId);
        $this->endRide($rideId);
        $location = new Location(1, 1);

        $handler = new RefreshBikeLocationHandler(
            $this->bikeRepository,
            new BikeLocationFetcherStub($location),
        );
        $handler(new RefreshBikeLocationCommand($bikeId->toInt()));

        $bike = $this->bikeRepository->getById($bikeId);
        self::assertEquals($location, $bike->location);
    }
}
