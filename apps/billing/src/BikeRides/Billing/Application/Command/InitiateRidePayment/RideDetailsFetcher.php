<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Application\Command\InitiateRidePayment;

use App\BikeRides\Billing\Domain\Model\RidePayment\RideDetails;
use App\BikeRides\Billing\Domain\Model\RidePayment\RideId;

interface RideDetailsFetcher
{
    public function fetch(RideId $rideId): RideDetails;
}
