<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Domain\Model\RidePayment;

interface RidePaymentGateway
{
    public function capture(RidePaymentId $ridePaymentId): ExternalPaymentRef;
}
