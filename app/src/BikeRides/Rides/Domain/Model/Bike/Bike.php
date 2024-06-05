<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Model\Bike;

use App\BikeRides\Shared\Domain\Model\BikeId;
use App\Foundation\Location;

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
