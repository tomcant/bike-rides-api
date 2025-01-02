<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Command\StartRide;

use BikeRides\SharedKernel\Domain\Model\BikeId;

interface BikeAvailabilityChecker
{
    public function isAvailable(BikeId $bikeId): bool;
}
