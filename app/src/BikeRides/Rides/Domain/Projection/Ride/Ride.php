<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Projection\Ride;

final class Ride
{
    public function __construct(
        public readonly string $rideId,
        public readonly string $riderId,
        public readonly string $bikeId,
        public readonly \DateTimeImmutable $startedAt,
        public ?\DateTimeImmutable $endedAt = null,
    ) {
    }

    public static function start(
        string $rideId,
        string $riderId,
        string $bikeId,
        \DateTimeImmutable $startedAt,
    ): self {
        return new self($rideId, $riderId, $bikeId, $startedAt);
    }

    public function end(\DateTimeImmutable $endedAt): void
    {
        $this->endedAt = $endedAt;
    }
}
