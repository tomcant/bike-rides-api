<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Infrastructure;

use App\BikeRides\Billing\Application\Command\InitiateRidePayment\RidePaymentDuplicateChecker;
use BikeRides\SharedKernel\Domain\Model\RideId;
use Doctrine\DBAL\Connection;

final readonly class PostgresRidePaymentDuplicateChecker implements RidePaymentDuplicateChecker
{
    public function __construct(private Connection $connection)
    {
    }

    public function isDuplicate(RideId $rideId): bool
    {
        return (bool) $this->connection->fetchOne(
            'SELECT TRUE FROM billing.projection_ride_payment WHERE ride_id = :ride_id',
            ['ride_id' => $rideId->toString()],
        );
    }
}
