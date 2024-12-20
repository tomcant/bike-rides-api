<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Projection\RideSummary;

use App\BikeRides\Rides\Domain\Model\Ride\Event;
use App\BikeRides\Shared\Domain\Helpers\AggregateEventsSubscriber;

final class RideSummaryProjector extends AggregateEventsSubscriber
{
    public function __construct(private readonly RideSummaryProjectionRepository $repository)
    {
    }

    protected function handleRideWasSummarised(Event\RideWasSummarised $event): void
    {
        $summary = new RideSummary(
            rideId: $event->getAggregateId()->toString(),
            duration: $event->summary->duration,
            route: $event->summary->route->toArray(),
        );

        $this->repository->store($summary);
    }
}
