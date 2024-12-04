<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Unit\Application\Command;

use App\BikeRides\Rides\Application\Command\RefreshBikeLocation\RefreshBikeLocationCommand;
use App\BikeRides\Rides\Application\Command\RefreshBikeLocation\RefreshBikeLocationHandler;
use App\BikeRides\Shared\Domain\Model\BikeId;
use App\BikeRides\Shared\Domain\Model\RideId;
use App\BikeRides\Shared\Domain\Model\RiderId;
use App\Foundation\Location;
use App\Tests\BikeRides\Rides\Doubles\BikeLocationFetcherStub;

final class RefreshBikeLocationTest extends CommandTestCase
{
    public function test_it_refreshes_bike_location(): void
    {
        $this->createRider($riderId = RiderId::fromString('rider_id'));
        $this->createBike($bikeId = BikeId::generate());
        $this->startRide($rideId = RideId::generate(), $riderId, $bikeId);
        $this->endRide($rideId);
        $location = new Location(1, 1);

        $handler = new RefreshBikeLocationHandler(
            $this->bikeRepository,
            new BikeLocationFetcherStub($location),
        );
        $handler(new RefreshBikeLocationCommand($bikeId->toString()));

        self::assertEquals($location, $this->bikeRepository->getById($bikeId)->location);
    }
}
