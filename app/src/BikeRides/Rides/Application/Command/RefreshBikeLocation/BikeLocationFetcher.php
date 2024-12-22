<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Command\RefreshBikeLocation;

use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\Location;

interface BikeLocationFetcher
{
    public function fetch(BikeId $bikeId): Location;
}
