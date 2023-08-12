<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Model\Bike;

use App\BikeRides\Rides\Domain\Model\Shared\BikeId;

interface BikeRepository
{
    public function store(Bike $bike): void;

    /** @throws BikeNotFound */
    public function getById(BikeId $bikeId): Bike;

    /** @return array<Bike> */
    public function list(): array;
}
