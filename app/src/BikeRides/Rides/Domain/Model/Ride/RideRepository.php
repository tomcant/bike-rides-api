<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Model\Ride;

use App\BikeRides\Shared\Domain\Helpers\AggregateName;
use App\BikeRides\Shared\Domain\Helpers\EventStore;
use App\BikeRides\Shared\Domain\Model\RideId;

final readonly class RideRepository
{
    public function __construct(private EventStore $eventStore)
    {
    }

    public function store(Ride $ride): void
    {
        $this->eventStore->store($ride->flushEvents());
    }

    public function getById(RideId $rideId): Ride
    {
        $events = $this->eventStore->get(AggregateName::fromString(Ride::AGGREGATE_NAME), $rideId);

        if ($events->isEmpty()) {
            throw new RideNotFound($rideId);
        }

        return Ride::buildFrom($events);
    }
}
