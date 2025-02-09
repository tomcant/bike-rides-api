<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Application\Command\RecordTrackingEvent;

use BikeRides\Foundation\Application\Command\Command;
use BikeRides\Foundation\Json;
use BikeRides\Foundation\Timestamp;
use BikeRides\SharedKernel\Domain\Model\BikeId;
use BikeRides\SharedKernel\Domain\Model\Location;

final readonly class RecordTrackingEventCommand implements Command
{
    public BikeId $bikeId;

    public function __construct(
        int $bikeId,
        public Location $location,
        public \DateTimeImmutable $trackedAt,
    ) {
        $this->bikeId = BikeId::fromInt($bikeId);
    }

    public function serialize(): string
    {
        return Json::encode([
            'bikeId' => $this->bikeId->toInt(),
            'location' => $this->location->toArray(),
            'trackedAt' => Timestamp::format($this->trackedAt),
        ]);
    }
}
