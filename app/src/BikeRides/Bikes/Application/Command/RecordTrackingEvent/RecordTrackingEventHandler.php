<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Application\Command\RecordTrackingEvent;

use App\BikeRides\Bikes\Domain\Model\TrackingEvent\TrackingEvent;
use App\BikeRides\Bikes\Domain\Model\TrackingEvent\TrackingEventRepository;
use App\BikeRides\Shared\Application\Command\CommandHandler;
use App\BikeRides\Shared\Domain\Event\BikeLocated;
use App\BikeRides\Shared\Domain\Helpers\DomainEventBus;

final readonly class RecordTrackingEventHandler implements CommandHandler
{
    public function __construct(
        private TrackingEventRepository $trackingEventRepository,
        private DomainEventBus $eventBus,
    ) {
    }

    public function __invoke(RecordTrackingEventCommand $command): void
    {
        $this->trackingEventRepository->store(
            new TrackingEvent(
                $command->bikeId,
                $command->location,
                $command->trackedAt,
            ),
        );

        $this->eventBus->publish(
            new BikeLocated(
                $command->bikeId->toString(),
                $command->location,
                $command->trackedAt,
            ),
        );
    }
}
