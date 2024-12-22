<?php

declare(strict_types=1);

namespace App\BikeRides\Billing\Domain\Model\RidePayment;

use BikeRides\Foundation\Domain\AggregateName;
use BikeRides\Foundation\Domain\EventStore;

final readonly class RidePaymentRepository
{
    public function __construct(private EventStore $eventStore)
    {
    }

    public function store(RidePayment $ridePayment): void
    {
        $this->eventStore->store($ridePayment->flushEvents());
    }

    public function getById(RidePaymentId $ridePaymentId): RidePayment
    {
        $events = $this->eventStore->get(AggregateName::fromString(RidePayment::AGGREGATE_NAME), $ridePaymentId);

        if ($events->isEmpty()) {
            throw new RidePaymentNotFound($ridePaymentId);
        }

        return RidePayment::buildFrom($events);
    }
}
