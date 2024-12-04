<?php

declare(strict_types=1);

namespace App\Tests\BikeRides\Bikes\Unit\Application\Command;

use App\BikeRides\Bikes\Application\Command\ActivateBike\ActivateBikeCommand;
use App\BikeRides\Bikes\Application\Command\ActivateBike\ActivateBikeHandler;
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
            $this->eventBus = new DomainEventBusSpy(),
        );
    }

    public function test_it_activates_a_bike(): void
    {
        $this->registerBike($bikeId = BikeId::generate());
        $location = new Location(0, 0);

        ($this->handler)(new ActivateBikeCommand($bikeId->toString(), $location));

        self::assertEquals($location, $this->bikeRepository->getById($bikeId)->location);

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
        $this->activateBike($bikeId, new Location(0, 0));

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Bike is already active');

        ($this->handler)(new ActivateBikeCommand($bikeId->toString(), new Location(0, 0)));
    }
}
