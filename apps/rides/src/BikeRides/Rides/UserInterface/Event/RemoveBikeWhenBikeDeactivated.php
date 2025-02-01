<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\UserInterface\Event;

use App\BikeRides\Rides\Application\Command\RemoveBike\RemoveBikeCommand;
use BikeRides\Foundation\Application\Command\CommandBus;
use BikeRides\Foundation\Domain\DomainEventSubscriber;
use BikeRides\SharedKernel\Domain\Event\BikeDeactivated;

final readonly class RemoveBikeWhenBikeDeactivated implements DomainEventSubscriber
{
    public function __construct(private CommandBus $bus)
    {
    }

    public function __invoke(BikeDeactivated $event): void
    {
        $this->bus->dispatch(new RemoveBikeCommand($event->bikeId));
    }
}
