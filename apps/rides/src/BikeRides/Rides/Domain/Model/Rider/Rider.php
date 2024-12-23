<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Model\Rider;

use BikeRides\SharedKernel\Domain\Model\RiderId;

final class Rider
{
    public function __construct(public RiderId $riderId)
    {
    }
}
