<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\Domain\Model\Ride;

use App\BikeRides\Shared\Domain\Helpers\Aggregate;
use App\BikeRides\Shared\Domain\Helpers\AggregateName;
use App\BikeRides\Shared\Domain\Model\BikeId;
use App\BikeRides\Shared\Domain\Model\RideDuration;
use App\BikeRides\Shared\Domain\Model\RideId;
use App\BikeRides\Shared\Domain\Model\RiderId;
use App\Foundation\Clock\Clock;

final class Ride extends Aggregate
{
    public const string AGGREGATE_NAME = 'ride';
    private RideId $rideId;
    private BikeId $bikeId;
    private \DateTimeImmutable $startedAt;
    private ?\DateTimeImmutable $endedAt;
    private ?Summary $summary;

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

    public function getRoute(): ?Route
    {
        return $this->summary?->route;
    }

    public function hasBeenSummarised(): bool
    {
        return null !== $this->summary;
    }

    public static function start(
        RideId $rideId,
        RiderId $riderId,
        BikeId $bikeId,
        BikeAvailabilityChecker $bikeAvailabilityChecker,
    ): self {
        if (!$bikeAvailabilityChecker->isAvailable($bikeId)) {
            throw new \DomainException(\sprintf('Bike "%s" is not available', $bikeId->toString()));
        }

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

    public function summarise(RouteFetcher $routeFetcher): void
    {
        if ($this->hasBeenSummarised()) {
            throw new \DomainException('Ride has already been summarised');
        }

        if (!$this->hasEnded()) {
            throw new \DomainException('Ride has not ended');
        }

        $summary = new Summary(
            RideDuration::fromStartAndEnd($this->startedAt, $this->endedAt),
            $routeFetcher->fetch($this),
        );

        $this->raise(
            new Event\RideWasSummarised(
                $this->getAggregateVersion(),
                $this->rideId,
                $summary,
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
        $this->summary = null;
    }

    protected function applyRideWasEnded(Event\RideWasEnded $event): void
    {
        $this->endedAt = $event->occurredAt;
    }

    protected function applyRideWasSummarised(Event\RideWasSummarised $event): void
    {
        $this->summary = $event->summary;
    }
}
