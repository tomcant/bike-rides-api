<?php declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Application\Command;

use App\BikeRides\Rides\Application\Command\EndRide\EndRideCommand;
use App\BikeRides\Rides\Application\Command\EndRide\EndRideHandler;
use App\BikeRides\Rides\Application\Command\RegisterBike\RegisterBikeCommand;
use App\BikeRides\Rides\Application\Command\RegisterBike\RegisterBikeHandler;
use App\BikeRides\Rides\Application\Command\StartRide\StartRideCommand;
use App\BikeRides\Rides\Application\Command\StartRide\StartRideHandler;
use App\BikeRides\Rides\Application\Command\StoreRider\StoreRiderCommand;
use App\BikeRides\Rides\Application\Command\StoreRider\StoreRiderHandler;
use App\BikeRides\Rides\Application\Command\TrackBike\TrackBikeCommand;
use App\BikeRides\Rides\Application\Command\TrackBike\TrackBikeHandler;
use App\BikeRides\Rides\Domain\Model\Bike\BikeRepository;
use App\BikeRides\Rides\Domain\Model\Ride\Event\RideEventFactory;
use App\BikeRides\Rides\Domain\Model\Ride\RideRepository;
use App\BikeRides\Rides\Domain\Model\Rider\RiderRepository;
use App\BikeRides\Rides\Domain\Model\Shared\BikeId;
use App\BikeRides\Rides\Domain\Model\Shared\RideId;
use App\BikeRides\Rides\Domain\Model\Shared\RiderId;
use App\BikeRides\Rides\Domain\Model\Track\TrackRepository;
use App\BikeRides\Shared\Domain\Helpers\DomainEvent;
use App\BikeRides\Shared\Domain\Helpers\EventStore;
use App\Foundation\Clock\Clock;
use App\Foundation\Location;
use App\Tests\BikeRides\Rides\Doubles\BikeAvailabilityCheckerStub;
use App\Tests\BikeRides\Rides\Doubles\InMemoryBikeRepository;
use App\Tests\BikeRides\Rides\Doubles\InMemoryRiderRepository;
use App\Tests\BikeRides\Rides\Doubles\InMemoryTrackRepository;
use App\Tests\BikeRides\Shared\Doubles\ClockStub;
use App\Tests\BikeRides\Shared\Doubles\DomainEventBusDummy;
use App\Tests\BikeRides\Shared\Doubles\InMemoryEventStore;
use PHPUnit\Framework\TestCase;

abstract class CommandTestCase extends TestCase
{
    protected readonly EventStore $eventStore;
    protected readonly RideRepository $rideRepository;
    protected readonly RiderRepository $riderRepository;
    protected readonly BikeRepository $bikeRepository;
    protected readonly TrackRepository $trackRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rideRepository = new RideRepository(
            new InMemoryEventStore(new RideEventFactory()),
        );
        $this->riderRepository = new InMemoryRiderRepository();
        $this->bikeRepository = new InMemoryBikeRepository();
        $this->trackRepository = new InMemoryTrackRepository();

        Clock::useClock(new ClockStub());
    }

    protected function storeRider(RiderId $riderId): void
    {
        $handler = new StoreRiderHandler($this->riderRepository);
        $handler(new StoreRiderCommand($riderId->toString()));
    }

    protected function registerBike(BikeId $bikeId): void
    {
        $handler = new RegisterBikeHandler($this->bikeRepository);
        $handler(new RegisterBikeCommand($bikeId->toString()));
    }

    protected function trackBike(BikeId $bikeId, Location $location): void
    {
        $handler = new TrackBikeHandler($this->trackRepository, new DomainEventBusDummy());
        $handler(new TrackBikeCommand($bikeId->toString(), $location, new \DateTimeImmutable('now')));
    }

    protected function startRide(RideId $rideId, RiderId $riderId, BikeId $bikeId): void
    {
        $handler = new StartRideHandler($this->rideRepository, BikeAvailabilityCheckerStub::available());
        $handler(new StartRideCommand($rideId->toString(), $riderId->toString(), $bikeId->toString()));
    }

    protected function endRide(RideId $rideId): void
    {
        $handler = new EndRideHandler($this->rideRepository, new DomainEventBusDummy());
        $handler(new EndRideCommand($rideId->toString()));
    }

    protected static function assertDomainEventEquals(DomainEvent $expected, DomainEvent $actual): void
    {
        static::assertEquals($expected->serialize(), $actual->serialize());
    }
}
