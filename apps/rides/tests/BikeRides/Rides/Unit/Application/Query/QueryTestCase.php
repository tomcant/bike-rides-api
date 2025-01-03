<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Unit\Application\Query;

use App\BikeRides\Rides\Domain\Model\Ride\Event\RideWasEnded;
use App\BikeRides\Rides\Domain\Model\Ride\Event\RideWasStarted;
use App\BikeRides\Rides\Domain\Model\Ride\Event\RideWasSummarised;
use App\BikeRides\Rides\Domain\Model\Ride\Summary;
use BikeRides\Foundation\Clock\Clock;
use BikeRides\Foundation\Domain\AggregateEvent;
use BikeRides\Foundation\Domain\AggregateEvents;
use BikeRides\Foundation\Domain\AggregateEventsSubscriber;
use BikeRides\Foundation\Domain\AggregateVersion;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\RideId;
use BikeRides\SharedKernel\Domain\Model\RiderId;
use PHPUnit\Framework\TestCase;

abstract class QueryTestCase extends TestCase
{
    private AggregateVersion $version;
    private AggregateEventsSubscriber $projector;

    protected function setUp(): void
    {
        parent::setUp();

        $this->version = AggregateVersion::zero();
    }

    final protected function useProjector(AggregateEventsSubscriber $projector): void
    {
        $this->projector = $projector;
    }

    protected function startRide(RideId $rideId, RiderId $riderId, BikeId $bikeId, \DateTimeImmutable $startedAt): void
    {
        $this->projectEvent(new RideWasStarted($this->version, $rideId, $riderId, $bikeId, $startedAt));
    }

    protected function endRide(RideId $rideId, \DateTimeImmutable $endedAt): void
    {
        $this->projectEvent(new RideWasEnded($this->version, $rideId, $endedAt));
    }

    protected function summariseRide(RideId $rideId, Summary $summary): void
    {
        $this->projectEvent(new RideWasSummarised($this->version, $rideId, $summary, Clock::now()));
    }

    private function projectEvent(AggregateEvent $event): void
    {
        ($this->projector)(new AggregateEvents([$event]));
        $this->version = $this->version->next();
    }
}
