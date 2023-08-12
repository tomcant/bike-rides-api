<?php declare(strict_types=1);

namespace App\Tests\BikeRides\Billing\Doubles;

use App\BikeRides\Billing\Domain\Model\RidePayment\RideDetails;
use App\BikeRides\Billing\Domain\Model\RidePayment\RideDetailsFetcher;
use App\BikeRides\Billing\Domain\Model\RidePayment\RideId;

final readonly class RideDetailsFetcherStub implements RideDetailsFetcher
{
    public function __construct(private RideDetails $rideDetails)
    {
    }

    public function fetch(RideId $rideId): RideDetails
    {
        return $this->rideDetails;
    }
}
