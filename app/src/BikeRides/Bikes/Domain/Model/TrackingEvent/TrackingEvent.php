<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Domain\Model\TrackingEvent;

use App\BikeRides\Shared\Domain\Model\BikeId;
use App\Foundation\Location;

final readonly class TrackingEvent
{
    public function __construct(
        public BikeId $bikeId,
        public Location $location,
        public \DateTimeImmutable $trackedAt,
    ) {
    }
}
