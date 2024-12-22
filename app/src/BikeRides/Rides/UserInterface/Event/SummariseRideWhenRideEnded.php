<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\UserInterface\Event;

use App\BikeRides\Rides\Application\Command\SummariseRide\SummariseRideCommand;
use App\BikeRides\Shared\Domain\Event\RideEnded;
use BikeRides\Foundation\Application\Command\CommandBus;
use BikeRides\Foundation\Domain\DomainEventSubscriber;

final readonly class SummariseRideWhenRideEnded implements DomainEventSubscriber
{
    public function __construct(private CommandBus $bus)
    {
    }

    public function __invoke(RideEnded $event): void
    {
        $this->bus->dispatch(new SummariseRideCommand($event->rideId));
    }
}
