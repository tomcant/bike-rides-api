<?php

declare(strict_types=1);

namespace App\BikeRides\Rides\UserInterface\Event;

use App\BikeRides\Rides\Application\Command\RefreshBikeLocation\RefreshBikeLocationCommand;
use App\BikeRides\Rides\Application\Query\GetBikeById;
use BikeRides\Foundation\Application\Command\CommandBus;
use BikeRides\Foundation\Domain\DomainEventSubscriber;
use BikeRides\SharedKernel\Domain\Event\RideEnded;

final readonly class RefreshBikeLocationWhenRideEnded implements DomainEventSubscriber
{
    public function __construct(
        private CommandBus $bus,
        private GetBikeById $getBikeById,
    ) {
    }

    public function __invoke(RideEnded $event): void
    {
        if (!$this->isBikeActive($event->bikeId)) {
            return;
        }

        $this->bus->dispatch(new RefreshBikeLocationCommand($event->bikeId));
    }

    private function isBikeActive(int $bikeId): bool
    {
        return null !== $this->getBikeById->query($bikeId);
    }
}
