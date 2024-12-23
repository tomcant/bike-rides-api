<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Domain\Model\Bike;

use BikeRides\SharedKernel\Domain\Model\BikeId;

interface BikeRepository
{
    public function store(Bike $bike): void;

    /** @throws BikeNotFound */
    public function getById(BikeId $bikeId): Bike;
}
