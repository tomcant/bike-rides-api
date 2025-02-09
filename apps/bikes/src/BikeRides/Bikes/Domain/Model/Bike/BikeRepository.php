<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Domain\Model\Bike;

use BikeRides\Foundation\Domain\CorrelationId;
use BikeRides\SharedKernel\Domain\Model\BikeId;

interface BikeRepository
{
    public function store(Bike $bike): void;

    /** @throws BikeNotFound */
    public function getById(BikeId $bikeId): Bike;

    /** @throws BikeNotFound */
    public function getByRegistrationCorrelationId(CorrelationId $correlationId): Bike;

    /** @return list<Bike> */
    public function list(): array;
}
