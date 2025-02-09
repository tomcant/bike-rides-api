<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Unit\Application\Command;

use App\BikeRides\Rides\Application\Command\CreateBike\CreateBikeCommand;
use App\BikeRides\Rides\Application\Command\CreateBike\CreateBikeHandler;
use App\BikeRides\Rides\Application\Command\CreateRider\CreateRiderCommand;
use App\BikeRides\Rides\Application\Command\CreateRider\CreateRiderHandler;
use App\BikeRides\Rides\Application\Command\EndRide\EndRideCommand;
use App\BikeRides\Rides\Application\Command\EndRide\EndRideHandler;
use App\BikeRides\Rides\Application\Command\StartRide\StartRideCommand;
use App\BikeRides\Rides\Application\Command\StartRide\StartRideHandler;
use App\BikeRides\Rides\Domain\Model\Bike\BikeRepository;
use App\BikeRides\Rides\Domain\Model\Ride\Event\RideEventFactory;
use App\BikeRides\Rides\Domain\Model\Ride\RideRepository;
use App\BikeRides\Rides\Domain\Model\Rider\RiderRepository;
use App\Tests\BikeRides\Rides\Doubles\BikeAvailabilityCheckerStub;
use App\Tests\BikeRides\Rides\Doubles\InMemoryBikeRepository;
use App\Tests\BikeRides\Rides\Doubles\InMemoryRiderRepository;
use App\Tests\BikeRides\Shared\Doubles\DomainEventBusDummy;
use BikeRides\Foundation\Clock\Clock;
use BikeRides\Foundation\Clock\ClockStub;
use BikeRides\Foundation\Domain\DomainEvent;
use BikeRides\Foundation\Domain\InMemoryEventStore;
use BikeRides\Foundation\Domain\TransactionBoundaryDummy;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\Location;
use BikeRides\SharedKernel\Domain\Model\RideId;
use BikeRides\SharedKernel\Domain\Model\RiderId;
use PHPUnit\Framework\TestCase;

abstract class CommandTestCase extends TestCase
{
    protected RideRepository $rideRepository;
    protected RiderRepository $riderRepository;
    protected BikeRepository $bikeRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rideRepository = new RideRepository(
            new InMemoryEventStore(new RideEventFactory()),
        );
        $this->riderRepository = new InMemoryRiderRepository();
        $this->bikeRepository = new InMemoryBikeRepository();

        Clock::useClock(new ClockStub());
    }

    protected function createRider(RiderId $riderId): void
    {
        $handler = new CreateRiderHandler($this->riderRepository);
        $handler(new CreateRiderCommand($riderId->toString()));
    }

    protected function createBike(BikeId $bikeId, Location $location = new Location(0, 0)): void
    {
        $handler = new CreateBikeHandler($this->bikeRepository);
        $handler(new CreateBikeCommand($bikeId->toInt(), $location));
    }

    protected function startRide(RideId $rideId, RiderId $riderId, BikeId $bikeId): void
    {
        $handler = new StartRideHandler($this->rideRepository, BikeAvailabilityCheckerStub::available());
        $handler(new StartRideCommand($rideId->toString(), $riderId->toString(), $bikeId->toInt()));
    }

    protected function endRide(RideId $rideId): void
    {
        $handler = new EndRideHandler($this->rideRepository, new TransactionBoundaryDummy(), new DomainEventBusDummy());
        $handler(new EndRideCommand($rideId->toString()));
    }

    protected static function assertDomainEventEquals(DomainEvent $expected, DomainEvent $actual): void
    {
        self::assertEquals($expected->serialize(), $actual->serialize());
    }
}
