<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Domain\Model\TrackingEvent;

use BikeRides\SharedKernel\Domain\Model\BikeId;

interface TrackingEventRepository
{
    public function store(TrackingEvent $event): void;

    public function getLastEventForBikeId(BikeId $bikeId): ?TrackingEvent;

    /** @return list<TrackingEvent> */
    public function getBetweenForBikeId(BikeId $bikeId, \DateTimeInterface $from, \DateTimeInterface $to): array;
}
