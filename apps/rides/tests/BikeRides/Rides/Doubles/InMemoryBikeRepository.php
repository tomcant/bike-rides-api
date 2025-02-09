<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Doubles;

use App\BikeRides\Rides\Domain\Model\Bike\Bike;
use App\BikeRides\Rides\Domain\Model\Bike\BikeNotFound;
use App\BikeRides\Rides\Domain\Model\Bike\BikeRepository;
use BikeRides\SharedKernel\Domain\Model\BikeId;

final class InMemoryBikeRepository implements BikeRepository
{
    /** @var array<int, Bike> */
    private array $bikes;

    public function store(Bike $bike): void
    {
        $this->bikes[$bike->bikeId->toInt()] = $bike;
    }

    public function getById(BikeId $bikeId): Bike
    {
        return $this->bikes[$bikeId->toInt()] ?? throw new BikeNotFound($bikeId);
    }

    public function remove(BikeId $bikeId): void
    {
        unset($this->bikes[$bikeId->toInt()]);
    }

    public function list(): array
    {
        return \array_values($this->bikes);
    }
}
