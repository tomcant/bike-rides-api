<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Application\Command\InitiateRidePayment;

use BikeRides\SharedKernel\Domain\Model\RideId;

interface RidePaymentDuplicateChecker
{
    public function isDuplicate(RideId $rideId): bool;
}
