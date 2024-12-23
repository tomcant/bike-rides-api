<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Projection\RideSummary;

use BikeRides\SharedKernel\Domain\Model\RideDuration;

final readonly class RideSummary
{
    /** @param array<int, array{latitude: float, longitude: float}> $route */
    public function __construct(
        public string $rideId,
        public RideDuration $duration,
        public array $route,
    ) {
    }
}
