<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Projection\RideSummary;

final class RideSummaryNotFound extends \DomainException
{
    public function __construct(string $rideId)
    {
        parent::__construct(\sprintf("Unable to find ride summary with ride id '%s'", $rideId));
    }
}
