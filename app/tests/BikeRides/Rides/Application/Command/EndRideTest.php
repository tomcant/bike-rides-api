<?php declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Application\Command;

use App\BikeRides\Rides\Application\Command\EndRide\EndRideCommand;
use App\BikeRides\Rides\Application\Command\EndRide\EndRideHandler;
use App\BikeRides\Rides\Domain\Model\Shared\BikeId;
use App\BikeRides\Rides\Domain\Model\Shared\RideId;
use App\BikeRides\Rides\Domain\Model\Shared\RiderId;
use App\BikeRides\Shared\Domain\Event\RideEnded;
use App\Tests\BikeRides\Shared\Doubles\DomainEventBusSpy;

final class EndRideTest extends CommandTestCase
{
    private EndRideHandler $handler;
    private DomainEventBusSpy $eventBus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = new EndRideHandler(
            $this->rideRepository,
            $this->eventBus = new DomainEventBusSpy(),
        );
    }

    public function test_it_ends_a_ride(): void
    {
        $this->storeRider($riderId = RiderId::fromString('rider_id'));
        $this->registerBike($bikeId = BikeId::generate());
        $this->startRide($rideId = RideId::generate(), $riderId, $bikeId);

        ($this->handler)(new EndRideCommand($rideId->toString()));

        self::assertTrue($this->rideRepository->getById($rideId)->hasEnded());

        self::assertDomainEventEquals(
            new RideEnded($rideId->toString()),
            $this->eventBus->lastEvent,
        );
    }

    public function test_it_cannot_end_a_ride_that_has_already_been_ended(): void
    {
        $this->storeRider($riderId = RiderId::fromString('rider_id'));
        $this->registerBike($bikeId = BikeId::generate());
        $this->startRide($rideId = RideId::generate(), $riderId, $bikeId);

        ($this->handler)(new EndRideCommand($rideId->toString()));

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Ride has already ended');

        ($this->handler)(new EndRideCommand($rideId->toString()));
    }
}
