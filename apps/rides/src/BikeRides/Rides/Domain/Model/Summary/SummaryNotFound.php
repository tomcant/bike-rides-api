<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Model\Summary;

use BikeRides\SharedKernel\Domain\Model\RideId;

final class SummaryNotFound extends \DomainException
{
    public static function forRideId(RideId $id): self
    {
        return new self("Unable to find summary for ride ID '{$id->toString()}'");
    }
}
