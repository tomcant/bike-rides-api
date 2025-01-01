<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Domain\Model\Bike;

use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\Location;

final class Bike
{
    public function __construct(
        public readonly BikeId $bikeId,
        public bool $isActive,
    ) {
    }

    public static function register(BikeId $bikeId): self
    {
        return new self($bikeId, isActive: false);
    }

    public function activate(?Location $location): void
    {
        if ($this->isActive) {
            throw CouldNotActivateBike::alreadyActive($this->bikeId);
        }

        if (null === $location) {
            throw CouldNotActivateBike::withoutTracking($this->bikeId);
        }

        $this->isActive = true;
    }
}
