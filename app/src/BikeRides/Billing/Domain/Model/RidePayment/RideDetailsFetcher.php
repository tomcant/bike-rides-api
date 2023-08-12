<?php declare(strict_types=1);

namespace App\BikeRides\Billing\Domain\Model\RidePayment;

interface RideDetailsFetcher
{
    public function fetch(RideId $rideId): RideDetails;
}
