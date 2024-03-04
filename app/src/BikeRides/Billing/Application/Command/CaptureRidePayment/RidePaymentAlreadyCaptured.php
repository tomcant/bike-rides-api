<?php declare(strict_types=1);

namespace App\BikeRides\Billing\Application\Command\CaptureRidePayment;

use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentAlreadyCaptured as RidePaymentAlreadyCapturedDomainException;

final class RidePaymentAlreadyCaptured extends \RuntimeException
{
    public static function fromDomainException(RidePaymentAlreadyCapturedDomainException $exception): self
    {
        return new self(\sprintf("Ride payment ID '%s' has already been captured", $exception->ridePaymentId->toString()));
    }
}
