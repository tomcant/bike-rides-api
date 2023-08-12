<?php declare(strict_types=1);

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
            $event->getAggregateId()->toString(),
            $event->riderId->toString(),
            $event->bikeId->toString(),
            $event->occurredAt,
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
