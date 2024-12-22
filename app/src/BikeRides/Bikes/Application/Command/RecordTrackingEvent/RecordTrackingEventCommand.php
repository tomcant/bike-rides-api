<?php

declare(strict_types=1);

namespace App\BikeRides\Bikes\Application\Command\RecordTrackingEvent;

use App\BikeRides\Shared\Domain\Model\BikeId;
use App\Foundation\Location;
use BikeRides\Foundation\Application\Command\Command;
use BikeRides\Foundation\Json;
use BikeRides\Foundation\Timestamp;

final readonly class RecordTrackingEventCommand implements Command
{
    public BikeId $bikeId;

    public function __construct(
        string $bikeId,
        public Location $location,
        public \DateTimeImmutable $trackedAt,
    ) {
        $this->bikeId = BikeId::fromString($bikeId);
    }

    public function serialize(): string
    {
        return Json::encode([
            'bikeId' => $this->bikeId->toString(),
            'location' => $this->location->toArray(),
            'trackedAt' => Timestamp::format($this->trackedAt),
        ]);
    }
}
