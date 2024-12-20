<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Doubles;

use App\BikeRides\Rides\Domain\Projection\RideSummary\RideSummary;
use App\BikeRides\Rides\Domain\Projection\RideSummary\RideSummaryNotFound;
use App\BikeRides\Rides\Domain\Projection\RideSummary\RideSummaryProjectionRepository;

final class InMemoryRideSummaryProjectionRepository implements RideSummaryProjectionRepository
{
    /** @var array<string, RideSummary> */
    private array $summaries = [];

    public function store(RideSummary $summary): void
    {
        $this->summaries[$summary->rideId] = $summary;
    }

    public function getByRideId(string $rideId): RideSummary
    {
        return $this->summaries[$rideId] ?? throw new RideSummaryNotFound($rideId);
    }
}
