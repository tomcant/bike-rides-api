<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Infrastructure;

use App\BikeRides\Rides\Domain\Model\Ride\BikeAvailabilityChecker;
use App\BikeRides\Shared\Domain\Model\BikeId;
use Doctrine\DBAL\Connection;

final readonly class PostgresBikeAvailabilityChecker implements BikeAvailabilityChecker
{
    public function __construct(private Connection $connection)
    {
    }

    public function isAvailable(BikeId $bikeId): bool
    {
        return ! $this
            ->connection
            ->fetchOne(
                '
                    SELECT TRUE
                    FROM rides.projection_ride
                    WHERE bike_id = :bike_id
                      AND ended_at IS NULL
                ',
                ['bike_id' => $bikeId->toString()],
            );
    }
}
