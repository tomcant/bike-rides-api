<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Projection\RideSummary;

interface RideSummaryProjectionRepository
{
    public function store(RideSummary $summary): void;

    /** @throws RideSummaryNotFound */
    public function getByRideId(string $rideId): RideSummary;
}
