<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Domain\Model\Bike;

use BikeRides\SharedKernel\Domain\Model\BikeId;

final class CouldNotDeactivateBike extends \DomainException
{
    private function __construct(BikeId $id, string $reason)
    {
        parent::__construct("Could not deactivate bike with ID '{$id->toString()}'. Reason: '{$reason}'");
    }

    public static function alreadyInactive(BikeId $id): self
    {
        return new self($id, 'Bike is already inactive');
    }
}
