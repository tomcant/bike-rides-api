<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Model\Bike;

use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\Location;

final class Bike
{
    public function __construct(
        public readonly BikeId $bikeId,
        public Location $location,
    ) {
    }

    public function locate(Location $location): void
    {
        $this->location = $location;
    }
}
