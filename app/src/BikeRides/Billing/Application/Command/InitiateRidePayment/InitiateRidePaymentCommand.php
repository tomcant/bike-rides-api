<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Application\Command\InitiateRidePayment;

use App\BikeRides\Billing\Domain\Model\RidePayment\RideId;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentId;
use BikeRides\Foundation\Application\Command\Command;
use BikeRides\Foundation\Json;

final readonly class InitiateRidePaymentCommand implements Command
{
    public RidePaymentId $ridePaymentId;
    public RideId $rideId;

    public function __construct(string $ridePaymentId, string $rideId)
    {
        $this->ridePaymentId = RidePaymentId::fromString($ridePaymentId);
        $this->rideId = RideId::fromString($rideId);
    }

    public function serialize(): string
    {
        return Json::encode([
            'ridePaymentId' => $this->ridePaymentId->toString(),
            'rideId' => $this->rideId->toString(),
        ]);
    }
}
