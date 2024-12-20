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

    /**
     * @return array{
     *   duration: array{
     *     startedAt: string,
     *     endedAt: string,
     *     minutes: int,
     *   },
     *   route: array<int, array{
     *     latitude: float,
     *     longitude: float,
     *   }>
     * }
     */
    public function toArray(): array
    {
        return [
            'duration' => $this->duration->toArray(),
            'route' => $this->route->toArray(),
        ];
    }

    /**
     * @param array{
     *   duration: array{
     *     startedAt: string,
     *     endedAt: string,
     *     minutes: int,
     *   },
     *   route: array<int, array{
     *     latitude: float,
     *     longitude: float,
     *   }>
     * } $summary
     */
    public static function fromArray(array $summary): self
    {
        return new self(
            RideDuration::fromArray($summary['duration']),
            new Route(\array_map(Location::fromArray(...), $summary['route'])),
        );
    }
}
