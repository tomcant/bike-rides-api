<?php declare(strict_types=1);

namespace App\Tests\BikeRides\Billing\Doubles;

use App\BikeRides\Billing\Domain\Model\RidePayment\ExternalPaymentRef;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentGateway;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentId;

final readonly class RidePaymentGatewayStub implements RidePaymentGateway
{
    public function __construct(private string $externalPaymentRef)
    {
    }

    public function capture(RidePaymentId $ridePaymentId): ExternalPaymentRef
    {
        return ExternalPaymentRef::fromString($this->externalPaymentRef);
    }
}
