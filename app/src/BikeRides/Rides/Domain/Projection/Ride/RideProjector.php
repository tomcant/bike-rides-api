<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Projection\Ride;

use App\BikeRides\Rides\Domain\Model\Ride\Event;
use App\BikeRides\Shared\Domain\Helpers\AggregateEventsSubscriber;

final class RideProjector extends AggregateEventsSubscriber
{
    public function __construct(private readonly RideProjectionRepository $repository)
    {
    }

    protected function handleRideWasStarted(Event\RideWasStarted $event): void
    {
        $ride = Ride::start(
            rideId: $event->getAggregateId()->toString(),
            riderId: $event->riderId->toString(),
            bikeId: $event->bikeId->toString(),
            startedAt: $event->occurredAt,
        );

        $this->repository->store($ride);
    }

    protected function handleRideWasEnded(Event\RideWasEnded $event): void
    {
        $ride = $this->repository->getById($event->getAggregateId()->toString());

        $ride->end($event->occurredAt);

        $this->repository->store($ride);
    }
}
