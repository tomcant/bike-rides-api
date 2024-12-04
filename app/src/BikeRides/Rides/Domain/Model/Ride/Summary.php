<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Model\Ride;

use App\BikeRides\Shared\Domain\Model\RideDuration;
use App\Foundation\Location;

final readonly class Summary
{
    public function __construct(
        public RideDuration $duration,
        public Route $route,
    ) {
    }

    public function toArray(): array
    {
        return [
            'duration' => $this->duration->toArray(),
            'route' => $this->route->toArray(),
        ];
    }

    public static function fromArray(array $summary): self
    {
        $route = new Route(
            \array_map(
                static fn ($location) => Location::fromArray($location),
                $summary['route'],
            ),
        );

        return new self(RideDuration::fromArray($summary['duration']), $route);
    }
}
