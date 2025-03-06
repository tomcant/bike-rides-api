<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Model\Summary;

use BikeRides\SharedKernel\Domain\Model\RideId;

interface SummaryRepository
{
    public function store(Summary $summary): void;

    /** @throws SummaryNotFound */
    public function getByRideId(RideId $rideId): Summary;
}
