<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Model\Ride;

use App\Foundation\Location;

final readonly class Route
{
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

    public function toArray(): array
    {
        return \array_map(
            static fn (Location $location) => $location->toArray(),
            $this->locations,
        );
    }
}
