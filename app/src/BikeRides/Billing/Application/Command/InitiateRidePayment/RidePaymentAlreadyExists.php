<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Application\Command\InitiateRidePayment;

use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentAlreadyExists as RidePaymentAlreadyExistsDomainException;

final class RidePaymentAlreadyExists extends \RuntimeException
{
    public static function fromDomainException(RidePaymentAlreadyExistsDomainException $exception): self
    {
        return new self(\sprintf("Duplicate payment for ride ID '%s'", $exception->rideId->toString()));
    }
}
