<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Domain\Model\TrackingEvent;

use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\Location;

final readonly class TrackingEvent
{
    public function __construct(
        public BikeId $bikeId,
        public Location $location,
        public \DateTimeImmutable $trackedAt,
    ) {
    }
}
