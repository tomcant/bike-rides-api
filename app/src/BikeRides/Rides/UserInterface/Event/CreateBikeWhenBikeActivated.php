<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\UserInterface\Event;

use App\BikeRides\Rides\Application\Command\CreateBike\CreateBikeCommand;
use BikeRides\Foundation\Application\Command\CommandBus;
use BikeRides\Foundation\Domain\DomainEventSubscriber;
use BikeRides\SharedKernel\Domain\Event\BikeActivated;

final readonly class CreateBikeWhenBikeActivated implements DomainEventSubscriber
{
    public function __construct(private CommandBus $bus)
    {
    }

    public function __invoke(BikeActivated $event): void
    {
        $this->bus->dispatch(new CreateBikeCommand($event->bikeId, $event->location));
    }
}
