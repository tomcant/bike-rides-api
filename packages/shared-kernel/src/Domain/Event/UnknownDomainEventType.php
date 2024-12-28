<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Domain\Model\Bike;

use BikeRides\SharedKernel\Domain\Model\BikeId;

final class UnknownDomainEventType extends \RuntimeException
{
    public function __construct(string $type)
    {
        parent::__construct("Unknown domain event type '{$type}'");
    }
}
