<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Domain\Model\RidePayment;

final class RidePaymentAlreadyExists extends \DomainException
{
    public function __construct(public readonly RideId $rideId)
    {
        parent::__construct(\sprintf("Duplicate payment for ride ID '%s'", $rideId->toString()));
    }
}
