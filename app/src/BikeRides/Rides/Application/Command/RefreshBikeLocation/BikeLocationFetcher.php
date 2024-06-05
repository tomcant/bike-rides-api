<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Command\RefreshBikeLocation;

use App\BikeRides\Shared\Domain\Model\BikeId;
use App\Foundation\Location;

interface BikeLocationFetcher
{
    public function fetch(BikeId $bikeId): Location;
}
