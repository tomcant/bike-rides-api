<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Application\Command\InitiateRidePayment;

use App\BikeRides\Billing\Domain\Model\RidePayment\RideId;

interface RidePaymentDuplicateChecker
{
    public function isDuplicate(RideId $rideId): bool;
}
