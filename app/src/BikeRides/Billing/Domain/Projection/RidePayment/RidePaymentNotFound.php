<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Domain\Projection\RidePayment;

final class RidePaymentNotFound extends \DomainException
{
    public static function forRidePaymentId(string $ridePaymentId): self
    {
        return new self(\sprintf("Unable to find ride payment with ID '%s'", $ridePaymentId));
    }

    public static function forRideId(string $rideId): self
    {
        return new self(\sprintf("Unable to find ride payment with ride id '%s'", $rideId));
    }
}
