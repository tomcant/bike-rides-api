<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Doubles;

use App\BikeRides\Bikes\Domain\Model\TrackingEvent\TrackingEvent;
use App\BikeRides\Bikes\Domain\Model\TrackingEvent\TrackingEventRepository;
use App\BikeRides\Shared\Domain\Model\BikeId;

final class InMemoryTrackingEventRepository implements TrackingEventRepository
{
    /** @var list<list<TrackingEvent>> */
    private array $events;

    public function store(TrackingEvent $event): void
    {
        $this->events[$event->bikeId->toString()][] = $event;
    }

    public function getBetweenForBikeId(BikeId $bikeId, \DateTimeInterface $from, \DateTimeInterface $to): array
    {
        return \array_filter(
            $this->events[$bikeId->toString()] ?? [],
            static fn ($event) => $from <= $event->trackedAt && $event->trackedAt <= $to,
        );
    }
}
