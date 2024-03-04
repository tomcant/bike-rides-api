<?php declare(strict_types=1);

namespace App\BikeRides\Billing\Domain\Projection\RidePayment;

final class RidePaymentNotFound extends \DomainException
{
    public function __construct(string $ridePaymentId)
    {
        parent::__construct(\sprintf("Unable to find ride payment with id '%s'", $ridePaymentId));
    }
}
