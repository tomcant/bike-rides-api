<?php declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Application\Query;

use App\BikeRides\Rides\Domain\Model\Ride\Event\RideWasEnded;
use App\BikeRides\Rides\Domain\Model\Ride\Event\RideWasStarted;
use App\BikeRides\Rides\Domain\Model\Ride\Event\RideWasSummarised;
use App\BikeRides\Rides\Domain\Model\Ride\Summary;
use App\BikeRides\Shared\Domain\Helpers\AggregateEvent;
use App\BikeRides\Shared\Domain\Helpers\AggregateEvents;
use App\BikeRides\Shared\Domain\Helpers\AggregateEventsSubscriber;
use App\BikeRides\Shared\Domain\Helpers\AggregateVersion;
use App\BikeRides\Shared\Domain\Model\BikeId;
use App\BikeRides\Shared\Domain\Model\RideId;
use App\BikeRides\Shared\Domain\Model\RiderId;
use PHPUnit\Framework\TestCase;

abstract class QueryTestCase extends TestCase
{
    private AggregateEvents $events;
    private AggregateVersion $version;

    protected function setUp(): void
    {
        parent::setUp();

        $this->events = new AggregateEvents([]);
        $this->version = AggregateVersion::zero();
    }

    protected function startRide(RideId $rideId, RiderId $riderId, BikeId $bikeId, \DateTimeImmutable $startedAt): void
    {
        $this->addEvent(new RideWasStarted($this->version, $rideId, $riderId, $bikeId, $startedAt));
    }

    protected function endRide(RideId $rideId, \DateTimeImmutable $endedAt): void
    {
        $this->addEvent(new RideWasEnded($this->version, $rideId, $endedAt));
    }

    protected function summariseRide(RideId $rideId, Summary $summary): void
    {
        $this->addEvent(new RideWasSummarised($this->version, $rideId, $summary, new \DateTimeImmutable('now')));
    }

    protected function runProjector(AggregateEventsSubscriber $projector): void
    {
        $projector($this->events);
    }

    private function addEvent(AggregateEvent $event): void
    {
        $this->events = $this->events->add($event);
        $this->version = $this->version->next();
    }
}
