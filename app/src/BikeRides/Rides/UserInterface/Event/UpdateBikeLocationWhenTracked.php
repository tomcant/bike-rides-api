<?php declare(strict_types=1);

namespace App\BikeRides\Rides\UserInterface\Event;

use App\BikeRides\Rides\Application\Command\UpdateBikeLocation\UpdateBikeLocationCommand;
use App\BikeRides\Shared\Application\Command\CommandBus;
use App\BikeRides\Shared\Domain\Event\BikeTracked;
use App\BikeRides\Shared\Domain\Helpers\DomainEventSubscriber;

final readonly class UpdateBikeLocationWhenTracked implements DomainEventSubscriber
{
    public function __construct(private CommandBus $bus)
    {
    }

    public function __invoke(BikeTracked $event): void
    {
        $this->bus->dispatch(new UpdateBikeLocationCommand($event->bikeId, $event->location));
    }
}
