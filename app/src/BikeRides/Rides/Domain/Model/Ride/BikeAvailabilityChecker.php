<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Model\Ride;

use App\BikeRides\Rides\Domain\Model\Shared\BikeId;

interface BikeAvailabilityChecker
{
    public function isAvailable(BikeId $bikeId): bool;
}
