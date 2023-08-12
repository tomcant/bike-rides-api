<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Model\Bike;

use App\BikeRides\Rides\Domain\Model\Shared\BikeId;
use App\Foundation\Location;

final class Bike
{
    public function __construct(
        public readonly BikeId $bikeId,
        public ?Location $location,
    ) {
    }

    public static function register(BikeId $bikeId): self
    {
        return new self($bikeId, location: null);
    }

    public function updateLocation(Location $location): void
    {
        $this->location = $location;
    }
}
