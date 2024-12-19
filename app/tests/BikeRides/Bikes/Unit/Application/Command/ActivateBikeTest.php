<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Unit\Application\Command;

use App\BikeRides\Bikes\Application\Command\ActivateBike\ActivateBikeCommand;
use App\BikeRides\Bikes\Application\Command\ActivateBike\ActivateBikeHandler;
use App\BikeRides\Bikes\Domain\Model\Bike\CouldNotActivateBike;
use App\BikeRides\Shared\Domain\Event\BikeActivated;
use App\BikeRides\Shared\Domain\Model\BikeId;
use App\Foundation\Location;
use App\Tests\BikeRides\Shared\Doubles\DomainEventBusSpy;

final class ActivateBikeTest extends CommandTestCase
{
    private ActivateBikeHandler $handler;
    private DomainEventBusSpy $eventBus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = new ActivateBikeHandler(
            $this->bikeRepository,
            $this->trackingEventRepository,
            $this->eventBus = new DomainEventBusSpy(),
        );
    }

    public function test_it_activates_a_bike(): void
    {
        $this->registerBike($bikeId = BikeId::generate());
        $this->recordTrackingEvent($bikeId, $location = new Location(0, 0));

        ($this->handler)(new ActivateBikeCommand($bikeId->toString()));

        self::assertTrue($this->bikeRepository->getById($bikeId)->isActive);

        self::assertDomainEventEquals(
            new BikeActivated(
                $bikeId->toString(),
                $location,
            ),
            $this->eventBus->lastEvent,
        );
    }

    public function test_it_cannot_activate_a_bike_that_is_already_active(): void
    {
        $this->registerBike($bikeId = BikeId::generate());
        $this->recordTrackingEvent($bikeId, new Location(0, 0));
        $this->activateBike($bikeId);

        self::expectException(CouldNotActivateBike::class);
        self::expectExceptionMessage("Could not activate bike with ID '{$bikeId->toString()}'. Reason: 'Bike is already active'");

        ($this->handler)(new ActivateBikeCommand($bikeId->toString()));
    }

    public function test_it_cannot_activate_a_bike_before_recording_a_track_event(): void
    {
        self::markTestSkipped();
    }
}
