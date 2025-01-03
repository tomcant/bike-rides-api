<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Application\Command\InitiateRidePayment;

use BikeRides\SharedKernel\Domain\Model\RideId;

final class RidePaymentAlreadyInitiated extends \RuntimeException
{
    public function __construct(public readonly RideId $rideId)
    {
        parent::__construct(\sprintf("Duplicate payment for ride ID '%s'", $rideId->toString()));
    }
}
