<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Model\Ride;

use App\BikeRides\Shared\Domain\Model\RideId;

final class RideNotFound extends \DomainException
{
    public function __construct(RideId $id)
    {
        parent::__construct(\sprintf("Unable to find ride with ID '%s'", $id->toString()));
    }
}
