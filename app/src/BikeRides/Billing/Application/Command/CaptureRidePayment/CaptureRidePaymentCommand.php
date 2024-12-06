<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Application\Command\CaptureRidePayment;

use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentId;
use App\BikeRides\Shared\Application\Command\Command;
use App\Foundation\Json;

final readonly class CaptureRidePaymentCommand implements Command
{
    public RidePaymentId $ridePaymentId;

    public function __construct(string $ridePaymentId)
    {
        $this->ridePaymentId = RidePaymentId::fromString($ridePaymentId);
    }

    public function serialize(): string
    {
        return Json::encode([
            'ridePaymentId' => $this->ridePaymentId->toString(),
        ]);
    }
}
