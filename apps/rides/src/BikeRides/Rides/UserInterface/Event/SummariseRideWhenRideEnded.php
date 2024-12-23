<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\UserInterface\Event;

use App\BikeRides\Rides\Application\Command\SummariseRide\SummariseRideCommand;
use BikeRides\Foundation\Application\Command\CommandBus;
use BikeRides\Foundation\Domain\DomainEventSubscriber;
use BikeRides\SharedKernel\Domain\Event\RideEnded;

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
