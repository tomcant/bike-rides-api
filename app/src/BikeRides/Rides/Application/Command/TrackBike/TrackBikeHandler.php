<?php declare(strict_types=1);

namespace App\BikeRides\Rides\Application\Command\TrackBike;

use App\BikeRides\Rides\Domain\Model\Track\Track;
use App\BikeRides\Rides\Domain\Model\Track\TrackRepository;
use App\BikeRides\Shared\Application\Command\CommandHandler;
use App\BikeRides\Shared\Domain\Event\BikeTracked;
use App\BikeRides\Shared\Domain\Helpers\DomainEventBus;

final readonly class TrackBikeHandler implements CommandHandler
{
    public function __construct(
        private TrackRepository $trackRepository,
        private DomainEventBus $eventBus,
    ) {
    }

    public function __invoke(TrackBikeCommand $command): void
    {
        $this->trackRepository->store(
            new Track(
                $command->bikeId,
                $command->location,
                $command->trackedAt,
            ),
        );

        $this->eventBus->publish(
            new BikeTracked(
                $command->bikeId->toString(),
                $command->location,
                $command->trackedAt,
            ),
        );
    }
}
