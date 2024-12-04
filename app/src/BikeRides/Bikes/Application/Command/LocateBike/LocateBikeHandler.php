<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Application\Command\LocateBike;

use App\BikeRides\Bikes\Domain\Model\BikeLocation\BikeLocation;
use App\BikeRides\Bikes\Domain\Model\BikeLocation\BikeLocationRepository;
use App\BikeRides\Shared\Application\Command\CommandHandler;
use App\BikeRides\Shared\Domain\Event\BikeLocated;
use App\BikeRides\Shared\Domain\Helpers\DomainEventBus;

final readonly class LocateBikeHandler implements CommandHandler
{
    public function __construct(
        private BikeLocationRepository $bikeLocationRepository,
        private DomainEventBus $eventBus,
    ) {
    }

    public function __invoke(LocateBikeCommand $command): void
    {
        $this->bikeLocationRepository->store(
            new BikeLocation(
                $command->bikeId,
                $command->location,
                $command->locatedAt,
            ),
        );

        $this->eventBus->publish(
            new BikeLocated(
                $command->bikeId->toString(),
                $command->location,
                $command->locatedAt,
            ),
        );
    }
}
