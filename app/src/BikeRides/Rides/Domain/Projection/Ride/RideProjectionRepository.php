<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Projection\Ride;

interface RideProjectionRepository
{
    public function store(Ride $ride): void;

    /** @throws RideNotFound */
    public function getById(string $rideId): Ride;
}
