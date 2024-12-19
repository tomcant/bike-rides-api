<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Model\Rider;

use App\BikeRides\Shared\Domain\Model\RiderId;

final class RiderNotFound extends \DomainException
{
    public function __construct(RiderId $id)
    {
        parent::__construct(\sprintf("Unable to find rider with ID '%s'", $id->toString()));
    }
}
