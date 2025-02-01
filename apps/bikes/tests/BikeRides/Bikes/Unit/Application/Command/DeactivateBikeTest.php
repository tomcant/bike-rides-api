<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Unit\Application\Command;

use App\BikeRides\Bikes\Application\Command\DeactivateBike\DeactivateBikeCommand;
use App\BikeRides\Bikes\Application\Command\DeactivateBike\DeactivateBikeHandler;
use App\BikeRides\Bikes\Domain\Model\Bike\CouldNotDeactivateBike;
use App\Tests\BikeRides\Shared\Doubles\DomainEventBusSpy;
use BikeRides\Foundation\Domain\TransactionBoundaryDummy;
use BikeRides\SharedKernel\Domain\Event\BikeDeactivated;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\Location;

final class DeactivateBikeTest extends CommandTestCase
{
    private DeactivateBikeHandler $handler;
    private DomainEventBusSpy $eventBus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = new DeactivateBikeHandler(
            $this->bikeRepository,
            new TransactionBoundaryDummy(),
            $this->eventBus = new DomainEventBusSpy(),
        );
    }

    public function test_it_deactivates_a_bike(): void
    {
        $this->registerBike($bikeId = BikeId::generate());
        $this->recordTrackingEvent($bikeId, $location = new Location(0, 0));
        $this->activateBike($bikeId);

        ($this->handler)(new DeactivateBikeCommand($bikeId->toString()));

        $bike = $this->bikeRepository->getById($bikeId);
        self::assertFalse($bike->isActive);
    }

    public function test_it_publishes_a_bike_deactivated_domain_event(): void
    {
        $this->registerBike($bikeId = BikeId::generate());
        $this->recordTrackingEvent($bikeId, $location = new Location(0, 0));
        $this->activateBike($bikeId);

        ($this->handler)(new DeactivateBikeCommand($bikeId->toString()));

        self::assertDomainEventEquals(
            new BikeDeactivated($bikeId->toString()),
            $this->eventBus->lastEvent,
        );
    }

    public function test_it_cannot_deactivate_a_bike_that_is_already_inactive(): void
    {
        $this->registerBike($bikeId = BikeId::generate());
        $this->recordTrackingEvent($bikeId, new Location(0, 0));

        self::expectException(CouldNotDeactivateBike::class);
        self::expectExceptionMessage("Could not deactivate bike with ID '{$bikeId->toString()}'. Reason: 'Bike is already inactive'");

        ($this->handler)(new DeactivateBikeCommand($bikeId->toString()));
    }
}
