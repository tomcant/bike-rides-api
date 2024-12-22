<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Model\Bike;

use BikeRides\SharedKernel\Domain\Model\BikeId;

final class BikeNotFound extends \DomainException
{
    public function __construct(BikeId $id)
    {
        parent::__construct(\sprintf("Unable to find bike with ID '%s'", $id->toString()));
    }
}
