<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Model\Ride;

use BikeRides\SharedKernel\Domain\Model\BikeId;

interface BikeAvailabilityChecker
{
    public function isAvailable(BikeId $bikeId): bool;
}
