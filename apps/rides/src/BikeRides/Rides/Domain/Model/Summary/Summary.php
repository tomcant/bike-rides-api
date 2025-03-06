<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Model\Summary;

use BikeRides\SharedKernel\Domain\Model\RideDuration;
use BikeRides\SharedKernel\Domain\Model\RideId;

final readonly class Summary
{
    public function __construct(
        public RideId $rideId,
        public RideDuration $duration,
        public Route $route,
    ) {
    }
}
