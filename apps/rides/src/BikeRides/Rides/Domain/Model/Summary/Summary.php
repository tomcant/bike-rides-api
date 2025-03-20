<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Model\Summary;

use BikeRides\SharedKernel\Domain\Model\RideDuration;
use BikeRides\SharedKernel\Domain\Model\RideId;
use Money\Money;

final class Summary
{
    public function __construct(
        public readonly RideId $rideId,
        public readonly RideDuration $duration,
        public readonly Route $route,
        public ?Money $price,
    ) {
    }
}
