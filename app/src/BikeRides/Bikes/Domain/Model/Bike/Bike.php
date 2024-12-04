<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Domain\Model\Bike;

use App\BikeRides\Shared\Domain\Model\BikeId;
use App\Foundation\Location;

final class Bike
{
    public function __construct(
        public readonly BikeId $bikeId,
        public ?Location $location,
        public bool $isActive,
    ) {
    }

    public static function register(BikeId $bikeId): self
    {
        return new self($bikeId, location: null, isActive: false);
    }

    public function activate(Location $location): void
    {
        if ($this->isActive) {
            throw new \DomainException('Bike is already active');
        }

        $this->isActive = true;
        $this->location = $location;
    }

    public function locate(Location $location): void
    {
        $this->location = $location;
    }
}
