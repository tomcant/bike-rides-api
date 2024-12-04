<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Application\Command\ActivateBike;

use App\BikeRides\Bikes\Domain\Model\Bike\BikeRepository;
use App\BikeRides\Shared\Application\Command\CommandHandler;
use App\BikeRides\Shared\Domain\Event\BikeActivated;
use App\BikeRides\Shared\Domain\Helpers\DomainEventBus;

final readonly class ActivateBikeHandler implements CommandHandler
{
    public function __construct(
        private BikeRepository $repository,
        private DomainEventBus $eventBus,
    ) {
    }

    public function __invoke(ActivateBikeCommand $command): void
    {
        $bike = $this->repository->getById($command->bikeId);

        $bike->activate($command->location);

        $this->repository->store($bike);

        $this->eventBus->publish(
            new BikeActivated(
                $command->bikeId->toString(),
                $command->location,
            ),
        );
    }
}
