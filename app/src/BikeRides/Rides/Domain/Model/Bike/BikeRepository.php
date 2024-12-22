<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Model\Bike;

use BikeRides\SharedKernel\Domain\Model\BikeId;

interface BikeRepository
{
    public function store(Bike $bike): void;

    /** @throws BikeNotFound */
    public function getById(BikeId $bikeId): Bike;

    /** @return list<Bike> */
    public function list(): array;
}
