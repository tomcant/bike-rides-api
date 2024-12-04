<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Infrastructure;

use App\BikeRides\Billing\Domain\Model\RidePayment\RideId;
use App\BikeRides\Billing\Domain\Model\RidePayment\RidePaymentDuplicateChecker;
use Doctrine\DBAL\Connection;

final readonly class EventStoreRidePaymentDuplicateChecker implements RidePaymentDuplicateChecker
{
    public function __construct(private Connection $connection)
    {
    }

    public function isDuplicate(RideId $rideId): bool
    {
        return (bool) $this
            ->connection
            ->fetchOne(
                "
                    SELECT TRUE
                    FROM billing.event_store
                    WHERE aggregate_name = 'ride_payment'
                      AND event_name = 'ride_payment.initiated'
                      AND event_data->>'rideId' = :ride_id
                ",
                ['ride_id' => $rideId->toString()],
            );
    }
}
