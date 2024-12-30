<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Doubles;

use App\BikeRides\Rides\Domain\Model\Ride\BikeAvailabilityChecker;
use BikeRides\SharedKernel\Domain\Model\BikeId;

final class BikeAvailabilityCheckerStub implements BikeAvailabilityChecker
{
    private function __construct(private bool $isAvailable)
    {
    }

    public static function available(): self
    {
        return new self(isAvailable: true);
    }

    public static function notAvailable(): self
    {
        return new self(isAvailable: false);
    }

    public function isAvailable(BikeId $bikeId): bool
    {
        return $this->isAvailable;
    }
}
