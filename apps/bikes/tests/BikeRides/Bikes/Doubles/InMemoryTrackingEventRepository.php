<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Doubles;

use App\BikeRides\Bikes\Domain\Model\TrackingEvent\TrackingEvent;
use App\BikeRides\Bikes\Domain\Model\TrackingEvent\TrackingEventRepository;
use BikeRides\SharedKernel\Domain\Model\BikeId;

final class InMemoryTrackingEventRepository implements TrackingEventRepository
{
    /** @var array<int, list<TrackingEvent>> */
    private array $events = [];

    public function store(TrackingEvent $event): void
    {
        $this->events[$event->bikeId->toInt()][] = $event;
    }

    public function getLastEventForBikeId(BikeId $bikeId): ?TrackingEvent
    {
        $events = $this->events[$bikeId->toInt()] ?? [];

        return \end($events) ?: null;
    }

    public function getBetweenForBikeId(BikeId $bikeId, \DateTimeInterface $from, \DateTimeInterface $to): array
    {
        return \array_values(
            \array_filter(
                $this->events[$bikeId->toInt()] ?? [],
                static fn ($event) => $from <= $event->trackedAt && $event->trackedAt <= $to,
            ),
        );
    }
}
