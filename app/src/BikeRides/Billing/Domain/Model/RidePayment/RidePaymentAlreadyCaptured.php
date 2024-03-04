<?php declare(strict_types=1);

namespace App\BikeRides\Billing\Domain\Model\RidePayment;

final class RidePaymentAlreadyCaptured extends \DomainException
{
    public function __construct(public readonly RidePaymentId $ridePaymentId)
    {
        parent::__construct(\sprintf("Ride payment ID '%s' has already been captured", $ridePaymentId->toString()));
    }
}
