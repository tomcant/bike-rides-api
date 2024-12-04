<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\UserInterface\Event;

use App\BikeRides\Rides\Application\Command\RefreshBikeLocation\RefreshBikeLocationCommand;
use App\BikeRides\Shared\Application\Command\CommandBus;
use App\BikeRides\Shared\Domain\Event\RideEnded;
use App\BikeRides\Shared\Domain\Helpers\DomainEventSubscriber;

final readonly class RefreshBikeLocationWhenRideEnded implements DomainEventSubscriber
{
    public function __construct(private CommandBus $bus)
    {
    }

    public function __invoke(RideEnded $event): void
    {
        $this->bus->dispatch(new RefreshBikeLocationCommand($event->bikeId));
    }
}
