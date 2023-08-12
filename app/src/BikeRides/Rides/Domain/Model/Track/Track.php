<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Model\Track;

use App\BikeRides\Rides\Domain\Model\Shared\BikeId;
use App\Foundation\Location;

final readonly class Track
{
    public function __construct(
        public BikeId $bikeId,
        public Location $location,
        public \DateTimeImmutable $trackedAt,
    ) {
    }
}
