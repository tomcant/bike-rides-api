<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Domain\Model\RidePayment;

interface RidePaymentDuplicateChecker
{
    public function isDuplicate(RideId $rideId): bool;
}
