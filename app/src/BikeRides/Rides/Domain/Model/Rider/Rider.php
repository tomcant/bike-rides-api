<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Model\Rider;

use App\BikeRides\Rides\Domain\Model\Shared\RiderId;

final class Rider
{
    public function __construct(public RiderId $riderId)
    {
    }
}
