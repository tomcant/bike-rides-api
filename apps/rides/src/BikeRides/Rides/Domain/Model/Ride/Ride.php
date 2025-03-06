<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Model\Ride;

use BikeRides\Foundation\Clock\Clock;
use BikeRides\Foundation\Domain\Aggregate;
use BikeRides\Foundation\Domain\AggregateName;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\RideId;
use BikeRides\SharedKernel\Domain\Model\RiderId;

final class Ride extends Aggregate
{
    public const string AGGREGATE_NAME = 'ride';
    private RideId $rideId;
    private BikeId $bikeId;
    private \DateTimeImmutable $startedAt;
    private ?\DateTimeImmutable $endedAt;

    public function getAggregateName(): AggregateName
    {
        return AggregateName::fromString(self::AGGREGATE_NAME);
    }

    public function getAggregateId(): RideId
    {
        return $this->rideId;
    }

    public function getBikeId(): BikeId
    {
        return $this->bikeId;
    }

    public function getStartedAt(): \DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function getEndedAt(): \DateTimeImmutable
    {
        return $this->endedAt;
    }

    public function hasEnded(): bool
    {
        return null !== $this->endedAt;
    }

    public static function start(RideId $rideId, RiderId $riderId, BikeId $bikeId): self
    {
        $ride = new self();

        $ride->raise(
            new Event\RideWasStarted(
                $ride->getAggregateVersion(),
                $rideId,
                $riderId,
                $bikeId,
                Clock::now(),
            ),
        );

        return $ride;
    }

    public function end(): void
    {
        if ($this->hasEnded()) {
            throw new \DomainException('Ride has already ended');
        }

        $this->raise(
            new Event\RideWasEnded(
                $this->getAggregateVersion(),
                $this->rideId,
                Clock::now(),
            ),
        );
    }

    protected function applyRideWasStarted(Event\RideWasStarted $event): void
    {
        $this->rideId = $event->getAggregateId();
        $this->bikeId = $event->bikeId;
        $this->startedAt = $event->occurredAt;
        $this->endedAt = null;
    }

    protected function applyRideWasEnded(Event\RideWasEnded $event): void
    {
        $this->endedAt = $event->occurredAt;
    }
}
