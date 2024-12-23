<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Domain\Model\Bike;

use BikeRides\SharedKernel\Domain\Model\BikeId;

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

    public function activate(): void
    {
        if ($this->isActive) {
            throw CouldNotActivateBike::alreadyActive($this->bikeId);
        }

        $this->isActive = true;
    }
}
