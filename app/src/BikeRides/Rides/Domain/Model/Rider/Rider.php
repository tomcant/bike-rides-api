<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Model\Rider;

use App\BikeRides\Shared\Domain\Model\RiderId;

final class Rider
{
    public function __construct(public RiderId $riderId)
    {
    }
}
