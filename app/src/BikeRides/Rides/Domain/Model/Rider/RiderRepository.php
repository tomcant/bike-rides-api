<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Model\Rider;

use App\BikeRides\Rides\Domain\Model\Shared\RiderId;

interface RiderRepository
{
    public function store(Rider $rider): void;

    /** @throws RiderNotFound */
    public function getById(RiderId $riderId): Rider;
}
