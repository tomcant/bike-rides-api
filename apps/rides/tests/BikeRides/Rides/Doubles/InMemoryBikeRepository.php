<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Doubles;

use App\BikeRides\Rides\Domain\Model\Bike\Bike;
use App\BikeRides\Rides\Domain\Model\Bike\BikeNotFound;
use App\BikeRides\Rides\Domain\Model\Bike\BikeRepository;
use BikeRides\SharedKernel\Domain\Model\BikeId;

final class InMemoryBikeRepository implements BikeRepository
{
    /** @var array<string, Bike> */
    private array $bikes;

    public function store(Bike $bike): void
    {
        $this->bikes[$bike->bikeId->toString()] = $bike;
    }

    public function getById(BikeId $bikeId): Bike
    {
        return $this->bikes[$bikeId->toString()] ?? throw new BikeNotFound($bikeId);
    }

    public function list(): array
    {
        return \array_values($this->bikes);
    }
}
