<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Domain\Model\BikeLocation;

use App\BikeRides\Shared\Domain\Model\BikeId;
use App\Foundation\Location;

final readonly class BikeLocation
{
    public function __construct(
        public BikeId $bikeId,
        public Location $location,
        public \DateTimeImmutable $locatedAt,
    ) {
    }
}
