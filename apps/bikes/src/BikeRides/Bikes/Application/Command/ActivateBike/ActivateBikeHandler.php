<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Application\Command\ActivateBike;

use App\BikeRides\Bikes\Domain\Model\Bike\BikeRepository;
use App\BikeRides\Bikes\Domain\Model\TrackingEvent\TrackingEventRepository;
use BikeRides\Foundation\Application\Command\CommandHandler;
use BikeRides\Foundation\Domain\DomainEventBus;
use BikeRides\SharedKernel\Domain\Event\BikeActivated;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\Location;

final readonly class ActivateBikeHandler implements CommandHandler
{
    public function __construct(
        private BikeRepository $bikeRepository,
        private TrackingEventRepository $trackingEventRepository,
        private DomainEventBus $eventBus,
    ) {
    }

    public function __invoke(ActivateBikeCommand $command): void
    {
        $bike = $this->bikeRepository->getById($command->bikeId);

        $bike->activate();

        $this->bikeRepository->store($bike);

        $this->eventBus->publish(
            new BikeActivated(
                $command->bikeId->toString(),
                $this->getLastBikeLocation($command->bikeId),
            ),
        );
    }

    private function getLastBikeLocation(BikeId $bikeId): Location
    {
        $lastTrackingEvent = $this->trackingEventRepository->getLastEventForBikeId($bikeId);

        return $lastTrackingEvent->location;
    }
}
