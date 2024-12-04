<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\UserInterface\Event;

use App\BikeRides\Bikes\Application\Command\UpdateBikeLocation\UpdateBikeLocationCommand;
use App\BikeRides\Shared\Application\Command\CommandBus;
use App\BikeRides\Shared\Domain\Event\BikeLocated;
use App\BikeRides\Shared\Domain\Helpers\DomainEventSubscriber;

final readonly class UpdateBikeLocationWhenBikeLocated implements DomainEventSubscriber
{
    public function __construct(private CommandBus $bus)
    {
    }

    public function __invoke(BikeLocated $event): void
    {
        $this->bus->dispatch(new UpdateBikeLocationCommand($event->bikeId, $event->location));
    }
}
