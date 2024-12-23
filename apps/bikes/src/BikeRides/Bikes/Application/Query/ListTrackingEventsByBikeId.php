<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Application\Query;

use App\BikeRides\Bikes\Domain\Model\TrackingEvent\TrackingEvent;
use App\BikeRides\Bikes\Domain\Model\TrackingEvent\TrackingEventRepository;
use BikeRides\SharedKernel\Domain\Model\BikeId;

final readonly class ListTrackingEventsByBikeId
{
    public function __construct(private TrackingEventRepository $repository)
    {
    }

    /**
     * @return list<array{
     *   location: array{
     *     latitude: float,
     *     longitude: float,
     *   },
     *   trackedAt: \DateTimeImmutable,
     * }>
     */
    public function query(string $bikeId, \DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        return \array_map(
            static fn (TrackingEvent $event) => [
                'location' => $event->location->toArray(),
                'trackedAt' => $event->trackedAt,
            ],
            $this->repository->getBetweenForBikeId(BikeId::fromString($bikeId), $from, $to),
        );
    }
}
