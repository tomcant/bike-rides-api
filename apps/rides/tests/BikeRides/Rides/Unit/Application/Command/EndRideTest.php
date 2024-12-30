<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Rides\Unit\Application\Command;

use App\BikeRides\Rides\Application\Command\EndRide\EndRideCommand;
use App\BikeRides\Rides\Application\Command\EndRide\EndRideHandler;
use App\Tests\BikeRides\Shared\Doubles\DomainEventBusSpy;
use BikeRides\SharedKernel\Domain\Event\RideEnded;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\RideId;
use BikeRides\SharedKernel\Domain\Model\RiderId;

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
        $this->createRider($riderId = RiderId::fromString('rider_id'));
        $this->createBike($bikeId = BikeId::generate());
        $this->startRide($rideId = RideId::generate(), $riderId, $bikeId);

        ($this->handler)(new EndRideCommand($rideId->toString()));

        self::assertTrue($this->rideRepository->getById($rideId)->hasEnded());
    }

    public function test_it_publishes_a_ride_ended_domain_event(): void
    {
        $this->createRider($riderId = RiderId::fromString('rider_id'));
        $this->createBike($bikeId = BikeId::generate());
        $this->startRide($rideId = RideId::generate(), $riderId, $bikeId);

        ($this->handler)(new EndRideCommand($rideId->toString()));

        self::assertDomainEventEquals(
            new RideEnded($rideId->toString(), $bikeId->toString()),
            $this->eventBus->lastEvent,
        );
    }

    public function test_it_cannot_end_a_ride_that_has_already_ended(): void
    {
        $this->createRider($riderId = RiderId::fromString('rider_id'));
        $this->createBike($bikeId = BikeId::generate());
        $this->startRide($rideId = RideId::generate(), $riderId, $bikeId);

        ($this->handler)(new EndRideCommand($rideId->toString()));

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Ride has already ended');

        ($this->handler)(new EndRideCommand($rideId->toString()));
    }
}
