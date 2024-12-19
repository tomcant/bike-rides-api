<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Application\Command\RecordTrackingEvent;

use App\BikeRides\Bikes\Domain\Model\TrackingEvent\TrackingEvent;
use App\BikeRides\Bikes\Domain\Model\TrackingEvent\TrackingEventRepository;
use App\BikeRides\Shared\Application\Command\CommandHandler;

final readonly class RecordTrackingEventHandler implements CommandHandler
{
    public function __construct(
        private TrackingEventRepository $trackingEventRepository,
    ) {
    }

    public function __invoke(RecordTrackingEventCommand $command): void
    {
        $event = new TrackingEvent($command->bikeId, $command->location, $command->trackedAt);

        $this->trackingEventRepository->store($event);
    }
}
