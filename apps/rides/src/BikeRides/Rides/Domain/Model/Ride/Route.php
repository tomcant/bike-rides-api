<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Model\Ride;

use BikeRides\SharedKernel\Domain\Model\Location;

final readonly class Route
{
    /** @var array<int, Location> */
    private array $locations;

    /** @param array<int, Location> $locations */
    public function __construct(array $locations)
    {
        \uksort(
            $locations,
            static fn ($timestampA, $timestampB) => $timestampA <=> $timestampB,
        );

        $this->locations = $locations;
    }

    /** @return array<int, array{latitude: float, longitude: float}> */
    public function toArray(): array
    {
        return \array_map(
            static fn (Location $location) => $location->toArray(),
            $this->locations,
        );
    }
}
