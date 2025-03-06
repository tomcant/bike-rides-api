<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Doubles;

use App\BikeRides\Rides\Domain\Model\Summary\Summary;
use App\BikeRides\Rides\Domain\Model\Summary\SummaryNotFound;
use App\BikeRides\Rides\Domain\Model\Summary\SummaryRepository;
use BikeRides\SharedKernel\Domain\Model\RideId;

final class InMemorySummaryRepository implements SummaryRepository
{
    /** @var array<string, Summary> */
    private array $summaries;

    public function store(Summary $summary): void
    {
        $this->summaries[$summary->rideId->toString()] = $summary;
    }

    public function getByRideId(RideId $rideId): Summary
    {
        return $this->summaries[$rideId->toString()] ?? throw SummaryNotFound::forRideId($rideId);
    }
}
