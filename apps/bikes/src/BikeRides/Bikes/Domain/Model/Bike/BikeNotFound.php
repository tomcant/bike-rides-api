<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Domain\Model\Bike;

use BikeRides\Foundation\Domain\CorrelationId;
use BikeRides\SharedKernel\Domain\Model\BikeId;

final class BikeNotFound extends \DomainException
{
    public static function forBikeId(BikeId $id): self
    {
        return new self("Unable to find bike with ID '{$id->toInt()}'");
    }

    public static function forRegistrationCorrelationId(CorrelationId $id): self
    {
        return new self("Unable to find bike with registration correlation ID '{$id->toString()}'");
    }
}
