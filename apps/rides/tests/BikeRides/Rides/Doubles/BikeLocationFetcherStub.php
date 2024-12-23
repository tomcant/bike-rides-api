<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Doubles;

use App\BikeRides\Rides\Application\Command\RefreshBikeLocation\BikeLocationFetcher;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\Location;

final class BikeLocationFetcherStub implements BikeLocationFetcher
{
    public function __construct(public Location $location = new Location(0, 0))
    {
    }

    public function fetch(BikeId $bikeId): Location
    {
        return $this->location;
    }

    public function useLocation(Location $location): void
    {
        $this->location = $location;
    }
}
