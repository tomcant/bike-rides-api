<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Projection\Ride;

final class RideNotFound extends \DomainException
{
    public function __construct(string $rideId)
    {
        parent::__construct("Unable to find ride with ID '{$rideId}'");
    }
}
