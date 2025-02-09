<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Doubles;

use App\BikeRides\Bikes\Domain\Model\Bike\Bike;
use App\BikeRides\Bikes\Domain\Model\Bike\BikeNotFound;
use App\BikeRides\Bikes\Domain\Model\Bike\BikeRepository;
use BikeRides\Foundation\Domain\CorrelationId;
use BikeRides\SharedKernel\Domain\Model\BikeId;

final class InMemoryBikeRepository implements BikeRepository
{
    /** @var array<int, Bike> */
    private array $bikes = [];

    public function store(Bike $bike): void
    {
        if (null === $bike->bikeId) {
            $bikeId = BikeId::fromInt(\count($this->bikes));
            $this->bikes[] = new Bike($bikeId, $bike->registrationCorrelationId, $bike->isActive);

            return;
        }

        $this->bikes[$bike->bikeId->toInt()] = $bike;
    }

    public function getById(BikeId $bikeId): Bike
    {
        return $this->bikes[$bikeId->toInt()] ?? throw BikeNotFound::forBikeId($bikeId);
    }

    public function getByRegistrationCorrelationId(CorrelationId $correlationId): Bike
    {
        foreach ($this->bikes as $bike) {
            if ($correlationId->equals($bike->registrationCorrelationId)) {
                return $bike;
            }
        }

        throw BikeNotFound::forRegistrationCorrelationId($correlationId);
    }

    public function list(): array
    {
        return \array_values($this->bikes);
    }
}
